@extends('admin.layout')

@section('title', 'Kullanicilar')

@section('content')
    <div class="top">
        <div>
            <p class="eyebrow">Musteriler</p>
            <h1>Kullanicilar</h1>
        </div>
    </div>

    <section class="panel">
        <table>
            <thead>
                <tr>
                    <th>Kullanici</th>
                    <th>E-posta</th>
                    <th>Siparis</th>
                    <th>Rol</th>
                    <th>Tarih</th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $user)
                    <tr>
                        <td>{{ $user->name }}</td>
                        <td>{{ $user->email }}</td>
                        <td>{{ $user->orders_count }}</td>
                        <td>{{ $user->is_admin ? 'Admin' : 'Musteri' }}</td>
                        <td>{{ $user->created_at?->format('d.m.Y H:i') }}</td>
                    </tr>
                @empty
                    <tr><td colspan="5">Kullanici yok.</td></tr>
                @endforelse
            </tbody>
        </table>
        <div class="pagination">{{ $users->links() }}</div>
    </section>
@endsection
