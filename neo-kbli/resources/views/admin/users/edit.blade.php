@extends('layouts.admin')
@section('title', 'Ubah Pengguna')

@section('content')
<div class="max-w-2xl">
    <div class="mb-6 flex items-center justify-between">
        <a href="{{ route('admin.users.index') }}" class="text-xs font-semibold text-gray-500 hover:text-orange-600 inline-flex items-center transition-colors group">
            <svg class="w-4 h-4 mr-2 transform group-hover:-translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            Kembali ke Daftar Pengguna
        </a>
    </div>

    @if(session('success'))
    <div class="mb-6 bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-md text-sm">{{ session('success') }}</div>
    @endif

    @if ($errors->any())
    <div class="mb-6 bg-red-50 text-red-700 p-4 rounded-md border border-red-100 text-sm">
        <ul class="list-disc pl-5 space-y-1">
            @foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach
        </ul>
    </div>
    @endif

    {{-- Edit Profile --}}
    <div class="bg-white rounded-md border border-gray-200 overflow-hidden mb-6">
        <div class="px-6 py-5 border-b border-gray-200 bg-gray-50">
            <h2 class="text-lg font-semibold text-gray-900">Ubah Pengguna: {{ $user->name }}</h2>
        </div>
        <form method="POST" action="{{ route('admin.users.update', $user->id) }}" class="p-6 space-y-5">
            @csrf
            @method('PUT')
            <div>
                <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap</label>
                <input type="text" name="name" id="name" value="{{ old('name', $user->name) }}" required class="w-full px-4 py-2.5 rounded-lg border border-border    ">
            </div>
            <div>
                <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                <input type="email" name="email" id="email" value="{{ old('email', $user->email) }}" required class="w-full px-4 py-2.5 rounded-lg border border-border    ">
            </div>
            <div>
                <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">Nomor Telepon</label>
                <input type="text" name="phone" id="phone" value="{{ old('phone', $user->phone) }}" class="w-full px-4 py-2.5 rounded-lg border border-border    ">
            </div>
            <div>
                <label for="role_id" class="block text-sm font-medium text-gray-700 mb-1">Peran</label>
                <select name="role_id" id="role_id" required class="w-full px-4 py-2.5 rounded-lg border border-border    ">
                    @foreach($roles as $role)
                    <option value="{{ $role->id }}" {{ $user->roles->contains('id', $role->id) ? 'selected' : '' }}>{{ $role->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="is_active" class="block text-sm font-medium text-gray-700 mb-1">Status Akun</label>
                <select name="is_active" id="is_active" required class="w-full px-4 py-2.5 rounded-lg border border-border    ">
                    <option value="1" {{ $user->is_active ? 'selected' : '' }}>Aktif</option>
                    <option value="0" {{ !$user->is_active ? 'selected' : '' }}>Nonaktif</option>
                </select>
            </div>
            <div class="pt-4 border-t border-gray-200">
                <button type="submit" class="px-6 py-2 bg-primary text-white text-sm font-semibold rounded-md hover:bg-primary-hover transition-colors border border-primary">Simpan Perubahan</button>
            </div>
        </form>
    </div>

    {{-- Reset Password --}}
    <div class="bg-white rounded-md border border-gray-200 overflow-hidden">
        <div class="px-6 py-5 border-b border-gray-200 bg-gray-50">
            <h2 class="text-lg font-semibold text-gray-900">Atur Ulang Kata Sandi</h2>
        </div>
        <form method="POST" action="{{ route('admin.users.resetPassword', $user->id) }}" class="p-6 space-y-5">
            @csrf
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Kata Sandi Baru</label>
                    <input type="password" name="password" id="password" required class="w-full px-4 py-2.5 rounded-lg border border-border    ">
                </div>
                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">Konfirmasi Kata Sandi</label>
                    <input type="password" name="password_confirmation" id="password_confirmation" required class="w-full px-4 py-2.5 rounded-lg border border-border    ">
                </div>
            </div>
            <div class="pt-4 border-t border-gray-200">
                <button type="submit" class="px-6 py-2 bg-red-600 text-white text-sm font-semibold rounded-md hover:bg-red-700 transition-colors border border-red-600">Reset Kata Sandi</button>
            </div>
        </form>
    </div>
</div>
@endsection
