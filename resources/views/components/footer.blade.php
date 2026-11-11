<footer class="site-footer">
    <div class="container">
        <div class="row gy-5 gx-lg-5">
            <div class="col-md-5">
                <img src="{{ asset('asset/img/Takari.png') }}" alt="Takari Wedding Organizer" class="logo">
                <p class="mb-0">Mewujudkan hari spesial dengan detail yang dikerjakan sepenuh hati.</p>
            </div>

            <div class="col-6 col-md-3 offset-md-1">
                <h2>Hubungi</h2>
                <ul class="list-unstyled mb-0">
                    <li><a href="https://www.instagram.com/takari_wo" target="_blank" rel="noopener"><i class="bi bi-instagram" aria-hidden="true"></i>@takari_wo</a></li>
                    <li><a href="https://wa.me/6287874217095" target="_blank" rel="noopener"><i class="bi bi-whatsapp" aria-hidden="true"></i>0878-7421-7095</a></li>
                    <li><i class="bi bi-geo-alt" aria-hidden="true"></i>Taktakan, Serang – Banten</li>
                </ul>
            </div>

            <div class="col-6 col-md-3">
                <h2>Jelajahi</h2>
                <ul class="list-unstyled mb-0">
                    <li><a href="{{ route('home') }}#about">Tentang Kami</a></li>
                    <li><a href="{{ route('home') }}#katalog">Katalog Paket</a></li>
                    <li><a href="{{ route('client.planner') }}">Wedding Planner</a></li>
                </ul>
            </div>
        </div>

        <div class="legal">&copy; {{ date('Y') }} Takari Wedding Organizer</div>
    </div>
</footer>

{{-- Disembunyikan di halaman kerja (planner) supaya tidak menutupi tombol aksi di HP; kontak tetap ada di footer --}}
@unless (Request::routeIs('client.planner*'))
<a href="https://wa.me/6287874217095" target="_blank" rel="noopener" class="whatsapp-float" aria-label="Hubungi kami via WhatsApp">
    <i class="bi bi-whatsapp" aria-hidden="true"></i>
</a>
@endunless
