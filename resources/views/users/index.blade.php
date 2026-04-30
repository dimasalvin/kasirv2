@extends('layouts.app')

@section('title', 'Manajemen User')
@section('page-title', 'Manajemen User')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <p class="text-gray-500 text-sm">Kelola akun pengguna sistem</p>
        <a href="{{ route('users.create') }}" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 text-sm font-medium">
            <i class="fas fa-plus mr-2"></i> Tambah User
        </a>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 border-b">
                <tr>
                    <th class="text-left py-3 px-4">Nama</th>
                    <th class="text-left py-3 px-4">Username</th>
                    <th class="text-left py-3 px-4">Email</th>
                    <th class="text-center py-3 px-4">Role</th>
                    <th class="text-center py-3 px-4">Status</th>
                    <th class="text-center py-3 px-4">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($users as $user)
                <tr class="border-b border-gray-50 hover:bg-gray-50">
                    <td class="py-3 px-4 font-medium">{{ $user->name }}</td>
                    <td class="py-3 px-4 font-mono text-xs">{{ $user->username }}</td>
                    <td class="py-3 px-4 text-gray-600">{{ $user->email }}</td>
                    <td class="py-3 px-4 text-center">
                        <span class="px-2 py-0.5 rounded text-xs font-medium
                            {{ $user->role == 'admin' ? 'bg-red-100 text-red-700' : ($user->role == 'apoteker' ? 'bg-blue-100 text-blue-700' : 'bg-green-100 text-green-700') }}">
                            {{ ucfirst(str_replace('_', ' ', $user->role)) }}
                        </span>
                    </td>
                    <td class="py-3 px-4 text-center">
                        @if($user->is_active)
                            <span class="px-2 py-0.5 bg-green-100 text-green-700 rounded text-xs">Aktif</span>
                        @else
                            <span class="px-2 py-0.5 bg-red-100 text-red-700 rounded text-xs">Nonaktif</span>
                        @endif
                    </td>
                    <td class="py-3 px-4 text-center">
                        <a href="{{ route('users.edit', $user) }}" class="p-1.5 text-yellow-600 hover:bg-yellow-50 rounded"><i class="fas fa-edit"></i></a>
                        @if($user->id !== auth()->id())
                        <form method="POST" action="{{ route('users.destroy', $user) }}" class="inline" onsubmit="return confirm('Hapus user ini?')">
                            @csrf @method('DELETE')
                            <button class="p-1.5 text-red-600 hover:bg-red-50 rounded"><i class="fas fa-trash"></i></button>
                        </form>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <div class="px-4 py-3 border-t">{{ $users->links() }}</div>
    </div>
</div>
@endsection
