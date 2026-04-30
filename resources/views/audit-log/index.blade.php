@extends('layouts.app')

@section('title', 'Audit Log')
@section('page-title', 'Audit Log Aktivitas')

@section('content')
<div class="space-y-6">
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 no-print">
        <form method="GET" class="flex flex-wrap gap-3 items-end">
            <div class="w-36">
                <label class="block text-xs text-gray-500 mb-1">Aksi</label>
                <select name="action" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                    <option value="">Semua</option>
                    <option value="login" {{ request('action') == 'login' ? 'selected' : '' }}>Login</option>
                    <option value="logout" {{ request('action') == 'logout' ? 'selected' : '' }}>Logout</option>
                    <option value="create" {{ request('action') == 'create' ? 'selected' : '' }}>Create</option>
                    <option value="update" {{ request('action') == 'update' ? 'selected' : '' }}>Update</option>
                    <option value="delete" {{ request('action') == 'delete' ? 'selected' : '' }}>Delete</option>
                    <option value="void" {{ request('action') == 'void' ? 'selected' : '' }}>Void</option>
                </select>
            </div>
            <div class="w-48">
                <label class="block text-xs text-gray-500 mb-1">User</label>
                <select name="user_id" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                    <option value="">Semua</option>
                    @foreach($users as $u)
                        <option value="{{ $u->id }}" {{ request('user_id') == $u->id ? 'selected' : '' }}>{{ $u->name }}</option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="px-4 py-2 bg-gray-800 text-white rounded-lg text-sm"><i class="fas fa-filter mr-1"></i> Filter</button>
        </form>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b">
                    <tr>
                        <th class="text-left py-2 px-3">Waktu</th>
                        <th class="text-left py-2 px-3">User</th>
                        <th class="text-center py-2 px-3">Aksi</th>
                        <th class="text-left py-2 px-3">Deskripsi</th>
                        <th class="text-left py-2 px-3">IP</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($logs as $log)
                    <tr class="border-b border-gray-50 hover:bg-gray-50">
                        <td class="py-2 px-3 whitespace-nowrap text-gray-500 text-xs">{{ $log->created_at->format('d/m/Y H:i:s') }}</td>
                        <td class="py-2 px-3">{{ $log->user->name ?? 'System' }}</td>
                        <td class="py-2 px-3 text-center">
                            @php
                                $colors = ['login' => 'bg-blue-100 text-blue-700', 'logout' => 'bg-gray-100 text-gray-700', 'create' => 'bg-green-100 text-green-700', 'update' => 'bg-yellow-100 text-yellow-700', 'delete' => 'bg-red-100 text-red-700', 'void' => 'bg-red-100 text-red-700'];
                            @endphp
                            <span class="px-2 py-0.5 rounded text-xs {{ $colors[$log->action] ?? 'bg-gray-100 text-gray-700' }}">{{ ucfirst($log->action) }}</span>
                        </td>
                        <td class="py-2 px-3">{{ $log->description }}</td>
                        <td class="py-2 px-3 font-mono text-xs text-gray-400">{{ $log->ip_address }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="5" class="py-8 text-center text-gray-500">Belum ada log</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-4 py-3 border-t">{{ $logs->withQueryString()->links() }}</div>
    </div>
</div>
@endsection
