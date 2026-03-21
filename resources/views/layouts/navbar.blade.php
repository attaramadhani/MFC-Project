@php
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

$cart_count = 0;
$cart_total = 0;

if (Auth::check()) {
    $id_user = Auth::id();

    $cart_count = DB::table('keranjang')
        ->where('id_user', $id_user)
        ->sum('jumlah');

    $cart_total = DB::table('keranjang as k')
        ->join('menu as m', 'm.id_menu', '=', 'k.id_menu')
        ->where('k.id_user', $id_user)
        ->selectRaw('SUM(k.jumlah * m.harga) as total')
        ->value('total') ?? 0;

    $uname = Auth::user()->nama_user ?? 'User';
    $initial = mb_strtoupper(mb_substr($uname, 0, 1));
}
@endphp

@if(Auth::check())
<button
    type="button"
    class="floating-cart-btn"
    id="btn-show-cart"
    data-bs-toggle="modal"
    data-bs-target="#cartModal"
>
    <span class="floating-cart-icon">🛒</span>

    <span
        class="floating-cart-badge {{ $cart_count > 0 ? '' : 'd-none' }}"
        id="cart-count-badge"
    >
        {{ $cart_count }}
    </span>
</button>
@endif

<nav class="navbar navbar-expand-lg fixed-top navbar-blur">
  <div class="container">

    @php
        $homeUrl = auth()->check() ? route('pelanggan.index') : url('/');
    @endphp

    <a class="navbar-brand" href="{{ $homeUrl }}#home">
        GeprekinAja
    </a>

    <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
      data-bs-target="#navbarNav">
      <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav nav-main mx-auto mb-2 mb-lg-0">
        <li class="nav-item">
          <a class="nav-link" href="{{ $homeUrl }}#menu">Menu</a>
        </li>

        @auth
        <li class="nav-item">
          <a class="nav-link" href="{{ route('pelanggan.orders.index') }}">Pesanan</a>
        </li>
        @endauth

        <li class="nav-item">
          <a class="nav-link" href="{{ $homeUrl }}#about">Tentang</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="{{ $homeUrl }}#contact">Kontak</a>
        </li>
      </ul>

      <ul class="navbar-nav ms-auto align-items-center">
        @auth
          <li class="nav-item me-2 d-none d-lg-block">
            <div class="user-chip">
              <div class="user-avatar">{{ $initial }}</div>
              <span>{{ $uname }}</span>
            </div>
          </li>

          <li class="nav-item">
            <form action="{{ route('logout') }}" method="POST">
              @csrf
              <button type="submit" class="btn btn-sm btn-outline-secondary rounded-pill">
                Logout
              </button>
            </form>
          </li>
        @else
          <li class="nav-item">
            <a href="{{ route('login') }}" class="btn btn-main text-white btn-sm rounded-pill">
              Login
            </a>
          </li>
        @endauth
      </ul>
    </div>
  </div>
</nav>

@if(Auth::check())
<div class="modal fade" id="cartModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
    <div class="modal-content rounded-4">

      <div class="modal-header border-0 pb-0">
        <h5 class="modal-title">Keranjang Pesanan</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <div class="modal-body" id="cartModalBody">
        <div class="text-center text-muted py-3 small">
          Memuat keranjang...
        </div>
      </div>

    </div>
  </div>
</div>
@endif