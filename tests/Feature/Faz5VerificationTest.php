<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Product;
use App\Models\Category;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Address;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Transaction;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class Faz5VerificationTest extends TestCase
{
    use RefreshDatabase;

    private $user;
    private $admin;
    private $category;
    private $product1;
    private $product2;

    protected function setUp(): void
    {
        parent::setUp();

        // Create category
        $this->category = Category::create([
            'name' => 'Posterler',
            'slug' => 'posterler',
            'description' => 'Film posterleri',
        ]);

        // Create products
        $this->product1 = Product::create([
            'category_id' => $this->category->id,
            'name' => 'Breaking Bad Poster',
            'slug' => 'breaking-bad-poster',
            'description' => 'Breaking Bad Walter White Poster',
            'price' => 100.00,
            'stock' => 10,
            'is_active' => true,
        ]);

        $this->product2 = Product::create([
            'category_id' => $this->category->id,
            'name' => 'Game of Thrones Poster',
            'slug' => 'game-of-thrones-poster',
            'description' => 'GOT Iron Throne Poster',
            'price' => 300.00,
            'stock' => 5,
            'is_active' => true,
        ]);

        // Create users
        $this->user = User::factory()->create([
            'email' => 'user1@fanstore.com',
            'balance' => 250.00,
            'role' => 'user',
            'is_active' => true,
        ]);

        $this->admin = User::factory()->create([
            'email' => 'admin@fanstore.com',
            'role' => 'admin',
            'is_active' => true,
        ]);
    }

    /**
     * TEST 1: Sepete Ürün Ekle
     */
    public function test_1_add_to_cart(): void
    {
        $response = $this->actingAs($this->user)
            ->postJson(route('cart.add'), [
                'product_id' => $this->product1->id,
                'quantity' => 1,
            ]);

        $response->assertOk()
            ->assertJson([
                'success' => true,
                'cartCount' => 1,
            ]);

        // Verify cart exists and item is added
        $cart = $this->user->cart;
        $this->assertNotNull($cart);
        $this->assertCount(1, $cart->items);
        $this->assertEquals($this->product1->id, $cart->items->first()->product_id);
    }

    /**
     * TEST 2: Sepet AJAX (Adet artır, azalt, sil, sepet boşalınca mesaj)
     */
    public function test_2_cart_ajax_operations(): void
    {
        $cart = Cart::create(['user_id' => $this->user->id]);
        $cartItem = CartItem::create([
            'cart_id' => $cart->id,
            'product_id' => $this->product1->id,
            'quantity' => 2,
        ]);

        // Increase quantity
        $response = $this->actingAs($this->user)
            ->putJson(route('cart.update', $cartItem), [
                'quantity' => 3,
            ]);

        $response->assertOk()
            ->assertJson([
                'success' => true,
                'subtotal' => 300.00,
                'total' => 300.00,
            ]);

        // Remove item
        $response = $this->actingAs($this->user)
            ->deleteJson(route('cart.remove', $cartItem));

        $response->assertOk()
            ->assertJson([
                'success' => true,
                'cartCount' => 0,
                'total' => 0.00,
            ]);
    }

    /**
     * TEST 3: Sepet Özeti (Bakiye, Ödenecek tutar vb.)
     */
    public function test_3_cart_page_summary_details(): void
    {
        $cart = Cart::create(['user_id' => $this->user->id]);
        CartItem::create([
            'cart_id' => $cart->id,
            'product_id' => $this->product1->id,
            'quantity' => 2, // Total 200.00 TL (less than 250.00 TL balance)
        ]);

        $response = $this->actingAs($this->user)
            ->get(route('cart.index'));

        $response->assertOk()
            ->assertSee('Sipariş Özeti')
            ->assertSee('250.00') // Balance shown
            ->assertSee('-200.00') // Balance to use
            ->assertSee('0.00') // Payable Amount
            ->assertSee('Siparişi Tamamla');
    }

    /**
     * TEST 4: Checkout Sayfası (Bakiye ve kart formu görünürlük kuralları)
     */
    public function test_4_checkout_page_displays_correct_information(): void
    {
        $cart = Cart::create(['user_id' => $this->user->id]);
        
        // Scenario A: Total 100 TL (less than 250 TL balance) -> Card form not required
        $item = CartItem::create([
            'cart_id' => $cart->id,
            'product_id' => $this->product1->id,
            'quantity' => 1,
        ]);

        $response = $this->actingAs($this->user)
            ->get(route('checkout.index'));

        $response->assertOk()
            ->assertSee('Cüzdan Kullanımı')
            ->assertSee('Kredi kartı bilgisi girmenize gerek yoktur.');

        // Scenario B: Total 400 TL (greater than 250 TL balance) -> Card form required
        $item->update(['quantity' => 4]); // 400.00 TL

        $response = $this->actingAs($this->user)
            ->get(route('checkout.index'));

        $response->assertOk()
            ->assertSee('Son Kullanma Tarihi')
            ->assertSee('Güvenlik Kodu (CVV)')
            ->assertSee('150.00 ₺'); // Card amount
    }

    /**
     * TEST 5: Sipariş Ver (Bakiye Yeterli)
     */
    public function test_5_place_order_with_balance_only(): void
    {
        $cart = Cart::create(['user_id' => $this->user->id]);
        CartItem::create([
            'cart_id' => $cart->id,
            'product_id' => $this->product1->id,
            'quantity' => 1, // 100 TL
        ]);

        $response = $this->actingAs($this->user)
            ->post(route('checkout.process'), [
                'shipping_address' => 'Caferağa Mah. Moda Cad. No:1 D:2 Kadıköy/İstanbul',
            ]);

        $order = Order::latest()->first();
        $this->assertNotNull($order);

        $response->assertRedirect(route('orders.show', $order));

        // Invoice No checks
        $expectedInvoice = 'FS-' . date('Ymd') . '-' . str_pad($order->id, 5, '0', STR_PAD_LEFT);
        $this->assertEquals($expectedInvoice, $order->invoice_no);
        $this->assertEquals('pending', $order->status);
        $this->assertEquals(100.00, $order->balance_used);
        $this->assertEquals(0.00, $order->card_amount);

        // Balance check
        $this->assertEquals(150.00, $this->user->refresh()->balance);

        // Transaction log check
        $transaction = Transaction::latest()->first();
        $this->assertNotNull($transaction);
        $this->assertEquals('payment', $transaction->type);
        $this->assertEquals(-100.00, $transaction->amount);
    }

    /**
     * TEST 6: Sipariş Ver (Kart Gerekli ve Validasyon)
     */
    public function test_6_place_order_with_card_required(): void
    {
        $cart = Cart::create(['user_id' => $this->user->id]);
        CartItem::create([
            'cart_id' => $cart->id,
            'product_id' => $this->product2->id,
            'quantity' => 1, // 300 TL (Needs 50 TL from card)
        ]);

        // Attempt without card info -> should fail validation
        $response = $this->actingAs($this->user)
            ->post(route('checkout.process'), [
                'shipping_address' => 'Caferağa Mah. Moda Cad. No:1 D:2 Kadıköy/İstanbul',
            ]);

        $response->assertSessionHasErrors(['card_number', 'card_expiry', 'card_cvv']);

        // Submit with card info
        $response = $this->actingAs($this->user)
            ->post(route('checkout.process'), [
                'shipping_address' => 'Caferağa Mah. Moda Cad. No:1 D:2 Kadıköy/İstanbul',
                'card_number' => '1234567890123456',
                'card_expiry' => '12/28',
                'card_cvv' => '123',
            ]);

        $order = Order::latest()->first();
        $this->assertNotNull($order);
        $response->assertRedirect(route('orders.show', $order));

        $this->assertEquals(250.00, $order->balance_used);
        $this->assertEquals(50.00, $order->card_amount);
        $this->assertEquals(0.00, $this->user->refresh()->balance);
    }

    /**
     * TEST 7: Stok Kontrolü
     */
    public function test_7_product_stock_is_decremented(): void
    {
        $cart = Cart::create(['user_id' => $this->user->id]);
        CartItem::create([
            'cart_id' => $cart->id,
            'product_id' => $this->product1->id,
            'quantity' => 3,
        ]);

        $initialStock = $this->product1->stock; // 10

        $this->actingAs($this->user)
            ->post(route('checkout.process'), [
                'shipping_address' => 'Caferağa Mah. Moda Cad. No:1 D:2 Kadıköy/İstanbul',
                'card_number' => '1234567890123456',
                'card_expiry' => '12/28',
                'card_cvv' => '123',
            ]);

        $this->assertEquals($initialStock - 3, $this->product1->refresh()->stock);
    }

    /**
     * TEST 8: Sipariş İptali (Geri yüklenen stok, bakiyeye iade, transaction)
     */
    public function test_8_order_cancellation_and_balance_refund(): void
    {
        $order = Order::create([
            'user_id' => $this->user->id,
            'total_amount' => 150.00,
            'balance_used' => 100.00,
            'card_amount' => 50.00,
            'status' => 'pending',
            'shipping_address' => 'Caferağa Mah. Moda Cad. No:1 D:2 Kadıköy/İstanbul',
            'invoice_no' => 'FS-20260526-00001',
        ]);

        OrderItem::create([
            'order_id' => $order->id,
            'product_id' => $this->product1->id,
            'quantity' => 2,
            'unit_price' => 75.00,
        ]);

        // Reduce stock in advance to simulate checkout
        $this->product1->decrement('stock', 2); // Now 8
        $this->user->decrement('balance', 100.00); // Now 150

        // Cancel order
        $response = $this->actingAs($this->user)
            ->delete(route('orders.cancel', $order));

        $response->assertRedirect(route('orders.index'));

        // Verify status
        $this->assertEquals('cancelled', $order->refresh()->status);

        // Verify stock is restored (8 + 2 = 10)
        $this->assertEquals(10, $this->product1->refresh()->stock);

        // Verify balance refund (entire total 150 refunded to balance: 150 + 150 = 300)
        $this->assertEquals(300.00, $this->user->refresh()->balance);

        // Verify transaction
        $transaction = Transaction::latest()->first();
        $this->assertNotNull($transaction);
        $this->assertEquals('refund', $transaction->type);
        $this->assertEquals(150.00, $transaction->amount);
    }

    /**
     * TEST 9: Sipariş Detay Sayfası
     */
    public function test_9_order_details_and_progress_bar(): void
    {
        $order = Order::create([
            'user_id' => $this->user->id,
            'total_amount' => 100.00,
            'balance_used' => 100.00,
            'card_amount' => 0.00,
            'status' => 'pending',
            'shipping_address' => 'Caferağa Mah. Moda Cad. No:1 D:2 Kadıköy/İstanbul',
            'invoice_no' => 'FS-20260526-00001',
        ]);

        $response = $this->actingAs($this->user)
            ->get(route('orders.show', $order));

        $response->assertOk()
            ->assertSee('FS-20260526-00001')
            ->assertSee('Siparişi İptal Et'); // Pending orders show cancel button
    }

    /**
     * TEST 10: Profil Sayfası
     */
    public function test_10_profile_page_tabs_and_updates(): void
    {
        $response = $this->actingAs($this->user)
            ->get(route('profile.show'));

        $response->assertOk()
            ->assertSee('Profil Bilgileri')
            ->assertSee('Cüzdan')
            ->assertSee('İşlemler')
            ->assertSee('Hesabı Pasifleştir');

        // Update info
        $response = $this->actingAs($this->user)
            ->put(route('profile.update'), [
                'name' => 'Emirhan Ok',
                'email' => 'emirhan@fanstore.com',
            ]);

        $response->assertRedirect(route('profile.show'));
        $this->assertEquals('Emirhan Ok', $this->user->refresh()->name);
        $this->assertEquals('emirhan@fanstore.com', $this->user->email);
    }

    /**
     * TEST 11: Avatar Yükleme
     */
    public function test_11_avatar_upload_resizing_and_webp_conversion(): void
    {
        if (!extension_loaded('gd') && !extension_loaded('imagick')) {
            $this->markTestSkipped('GD or Imagick extension is not installed.');
        }

        Storage::fake('public');

        $response = $this->actingAs($this->user)
            ->post(route('profile.avatar'), [
                'avatar' => UploadedFile::fake()->image('avatar.png', 500, 500),
            ]);

        $response->assertRedirect(route('profile.show'));

        $user = $this->user->refresh();
        $this->assertNotNull($user->avatar);
        $this->assertStringStartsWith('avatars/', $user->avatar);
        $this->assertStringEndsWith('.webp', $user->avatar);

        // Check if file exists in simulated disk
        Storage::disk('public')->assertExists($user->avatar);
    }

    /**
     * TEST 12: Adres Yönetimi
     */
    public function test_12_address_management_crud(): void
    {
        // Store
        $response = $this->actingAs($this->user)
            ->post(route('addresses.store'), [
                'title' => 'Ev Adresim',
                'full_address' => 'Caferağa Mah. Moda Cad. No:1 D:2',
                'city' => 'İstanbul',
                'district' => 'Kadıköy',
                'zip' => '34710',
            ]);

        $response->assertRedirect();
        
        $address = Address::latest()->first();
        $this->assertNotNull($address);
        $this->assertEquals('Ev Adresim', $address->title);
        $this->assertTrue($address->is_default); // First address is default

        // Set default toggle (AJAX)
        $address2 = Address::create([
            'user_id' => $this->user->id,
            'title' => 'İş Adresim',
            'full_address' => 'Büyükdere Cad. No:100',
            'city' => 'İstanbul',
            'district' => 'Şişli',
            'is_default' => false,
        ]);

        $response = $this->actingAs($this->user)
            ->postJson(route('addresses.default', $address2));

        $response->assertOk()
            ->assertJson(['success' => true]);

        $this->assertTrue($address2->refresh()->is_default);
        $this->assertFalse($address->refresh()->is_default);

        // Delete
        $response = $this->actingAs($this->user)
            ->delete(route('addresses.destroy', $address));

        $response->assertRedirect();
        $this->assertDatabaseMissing('addresses', ['id' => $address->id]);
    }

    /**
     * TEST 13: Admin Tracking Enum & User Progress Bar
     */
    public function test_13_admin_order_approval_and_tracking_flow(): void
    {
        $order = Order::create([
            'user_id' => $this->user->id,
            'total_amount' => 100.00,
            'balance_used' => 100.00,
            'card_amount' => 0.00,
            'status' => 'pending',
            'shipping_address' => 'Caferağa Mah. Moda Cad. No:1 D:2 Kadıköy/İstanbul',
            'invoice_no' => 'FS-20260526-00001',
        ]);

        // Admin approves order
        $response = $this->actingAs($this->admin)
            ->post(route('admin.orders.approve', $order));

        $response->assertRedirect();
        $this->assertEquals('approved', $order->refresh()->status);
        $this->assertNotNull($order->tracking);
        $this->assertEquals('sourcing', $order->tracking->step); // Starts at sourcing

        // Admin advances step: sourcing -> packaging
        $response = $this->actingAs($this->admin)
            ->post(route('admin.orders.advance', $order));

        $response->assertRedirect();
        $this->assertEquals('packaging', $order->tracking->refresh()->step);

        // User gets detail page and asserts steps rendered
        $response = $this->actingAs($this->user)
            ->get(route('orders.show', $order));

        $response->assertOk()
            ->assertSee('Tedarik Ediliyor')
            ->assertSee('Kutulanıyor');
    }

    /**
     * TEST 14: CartCount Dinamik
     */
    public function test_14_cart_count_dynamic_behavior(): void
    {
        // 1. Guest user cartCount is not visible (or equal to 0/empty)
        $response = $this->get(route('home'));
        $response->assertOk()
            ->assertDontSee('class="cart-badge"'); // Badge shouldn't exist since cart is empty/guest

        // 2. Add item to user cart and view page
        $cart = Cart::create(['user_id' => $this->user->id]);
        CartItem::create([
            'cart_id' => $cart->id,
            'product_id' => $this->product1->id,
            'quantity' => 3,
        ]);

        $response = $this->actingAs($this->user)
            ->get(route('home'));

        $response->assertOk()
            ->assertSee('<span class="cart-badge">3</span>', false);
    }
}
