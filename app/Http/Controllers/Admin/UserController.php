<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;

class UserController extends Controller
{
    /**
     * Display a listing of users.
     */
    public function index()
    {
        // To be implemented
    }

    /**
     * Display the specified user.
     */
    public function show(User $user)
    {
        // To be implemented
    }

    /**
     * Show the form for editing the specified user.
     */
    public function edit(User $user)
    {
        // To be implemented
    }

    /**
     * Update the specified user in storage.
     */
    public function update(Request $request, User $user)
    {
        // To be implemented
    }

    /**
     * Remove the specified user from storage.
     */
    public function destroy(User $user)
    {
        // To be implemented
    }

    /**
     * Toggle active status of a user.
     */
    public function toggleStatus(User $user)
    {
        // To be implemented
    }
}
