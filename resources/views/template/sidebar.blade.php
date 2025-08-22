<nav id="sidebar" class="sidebar js-sidebar">

    <div class="sidebar-content js-simplebar">

        <a class="sidebar-brand" href="{{ route('dashboard.index') }}">
            <span class="align-middle">MyERP</span>
        </a>

        <ul class="sidebar-nav">

            <li class="sidebar-item @yield('dashboard-active')">
                <a class="sidebar-link" href="{{ route('dashboard.index') }}">
                    <i class="bi bi-speedometer2 align-middle"></i>
                    <span class="align-middle">Dashboard</span>
                </a>
            </li>

            @if(Auth()->user()->role->akses_edit_setting)
                <li class="sidebar-item @yield('settings-active')">
                    <a class="sidebar-link" href="{{ route('settings.edit', 1) }}">
                        <i class="bi bi-gear-fill align-middle"></i>
                        <span class="align-middle">Setting</span>
                    </a>
                </li>
            @endif

            @if (Auth()->user()->role->akses_daftar_jabatan)
                <li class="sidebar-item @yield('roles-active')">
                    <a class="sidebar-link" href="{{ route('roles.index') }}">
                        <i class="bi bi-person-badge align-middle"></i>
                        <span class="align-middle">Jabatan</span>
                    </a>
                </li>
            @endif

            @if (Auth()->user()->role->akses_daftar_user)
                <li class="sidebar-item @yield('users-active')">
                    <a class="sidebar-link" href="{{ route('users.index') }}">
                        <i class="bi bi-people align-middle"></i>
                        <span class="align-middle">User</span>
                    </a>
                </li>
            @endif

            @if (Auth()->user()->role->akses_daftar_akun)
                <li class="sidebar-item @yield('accounts-active')">
                    <a class="sidebar-link" href="{{ route('accounts.index') }}">
                        <i class="bi bi-wallet2 align-middle"></i>
                        <span class="align-middle">Akun</span>
                    </a>
                </li>
            @endif

            @if (Auth()->user()->role->akses_daftar_jurnal)
                <li class="sidebar-item @yield('journals-active')">
                    <a class="sidebar-link" href="{{ route('journals.index') }}">
                        <i class="bi bi-journal-check align-middle"></i>
                        <span class="align-middle">Jurnal</span>
                    </a>
                </li>
            @endif

            @if (Auth()->user()->role->akses_daftar_gudang)
                <li class="sidebar-item @yield('warehouses-active')">
                    <a class="sidebar-link" href="{{ route('warehouses.index') }}">
                        <i class="bi bi-box-seam align-middle"></i>
                        <span class="align-middle">Gudang</span>
                    </a>
                </li>
            @endif

            @if (Auth()->user()->role->akses_daftar_satuan)
                <li class="sidebar-item @yield('units-active')">
                    <a class="sidebar-link" href="{{ route('units.index') }}">
                        <i class="bi bi-basket align-middle"></i>
                        <span class="align-middle">Satuan</span>
                    </a>
                </li>
            @endif

            @if (Auth()->user()->role->akses_daftar_kategori_barang)
                <li class="sidebar-item @yield('item_categories-active')">
                    <a class="sidebar-link" href="{{ route('item_categories.index') }}">
                        <i class="bi bi-collection align-middle"></i>
                        <span class="align-middle">Kategori Barang</span>
                    </a>
                </li>
            @endif

            @if (Auth()->user()->role->akses_daftar_barang)
                <li class="sidebar-item @yield('items-active')">
                    <a class="sidebar-link" href="{{ route('items.index') }}">
                        <i class="bi bi-box align-middle"></i>
                        <span class="align-middle">Barang</span>
                    </a>
                </li>
            @endif

            @if (Auth()->user()->role->akses_daftar_transaksi_barang)
                <li class="sidebar-item @yield('item_transactions-active')">
                    <a class="sidebar-link" href="{{ route('item_transactions.index') }}">
                        <i class="bi bi-arrow-left-right align-middle"></i>
                        <span class="align-middle">Transaksi Barang</span>
                    </a>
                </li>
            @endif

            @if(Auth::user()->role->akses_daftar_kontak)
                <li class="sidebar-item @yield('contacts-active')">
                    <a class="sidebar-link" href="{{ route('contacts.index') }}">
                        <i class="bi bi-person-lines-fill align-middle"></i>
                        <span class="align-middle">Kontak</span>
                    </a>
                </li>
            @endif


        </ul>


    </div>

</nav>
