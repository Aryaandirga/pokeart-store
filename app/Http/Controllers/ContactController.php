<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Inertia\Inertia;

class ContactController extends Controller
{
    public function index()
    {
        return Inertia::render('Contact');
    }

    public function send(Request $request)
    {
        $request->validate([
            'name'    => 'required|string|max:100',
            'email'   => 'required|email',
            'subject' => 'required|string|max:150',
            'message' => 'required|string|max:2000',
        ]);

        // Nanti bisa dikirim via email / notif
        // Untuk sekarang kita simpan ke log dulu
        \Log::info('Contact Form', $request->only(['name', 'email', 'subject', 'message']));

        return back()->with('success', 'Pesan berhasil dikirim! Kami akan menghubungi kamu segera.');
    }
}