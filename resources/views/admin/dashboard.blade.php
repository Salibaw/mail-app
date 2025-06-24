@extends('admin.layouts.app')

@section('title', 'Dashboard Admin')

@section('page-title', 'Dashboard')

@section('content')
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
    <!-- Card Contoh -->
    <div class="bg-white p-6 rounded-lg shadow">
        <h3 class="text-lg font-semibold text-gray-800">Total User</h3>
        <p class="text-3xl font-bold text-indigo-800">150</p>
    </div>
    <div class="bg-white p-6 rounded-lg shadow">
        <h3 class="text-lg font-semibold text-gray-800">Surat Masuk</h3>
        <p class="text-3xl font-bold text-indigo-800">75</p>
    </div>
    <div class="bg-white p-6 rounded-lg shadow">
        <h3 class="text-lg font-semibold text-gray-800">Surat Keluar</h3>
        <p class="text-3xl font-bold text-indigo-800">50</p>
    </div>
</div>
@endsection