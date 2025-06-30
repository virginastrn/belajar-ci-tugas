<!-- ======= Sidebar ======= -->
<aside id="sidebar" class="sidebar">

    <ul class="sidebar-nav" id="sidebar-nav">

        <li class="nav-item">
            <a class="nav-link <?php echo (uri_string() == '') ? "" : "collapsed" ?>" href="/home">
            <i class="bi bi-grid"></i>
            <span>Home</span>
            </a>
        </li>

        <li class="nav-item">
            <a class="nav-link <?php echo (uri_string() == 'keranjang') ? "" : "collapsed" ?>" href="keranjang">
            <i class="bi bi-cart-check"></i>
            <span>Keranjang</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?php echo (uri_string() == 'kategoriproduct') ? "" : "collapsed" ?>" href="kategoriproduct">
            <i class="bi bi-cart-check"></i>
            <span>Kategori Produk</span>
            </a>
        </li>   
        <?php
        if (session()->get('role') == 'admin') {
        ?>
            <li class="nav-item">
                <a class="nav-link <?php echo (uri_string() == 'produk') ? "" : "collapsed" ?>" href="produk">
                    <i class="bi bi-receipt"></i>
                    <span>Produk</span>
                </a>
        <li class="nav-item">
    <a class="nav-link" href="<?= base_url('/faq') ?>">
      <i class="bi bi-question-circle"></i> FAQ
    </a>
  </li>

  <li class="nav-item">
    <a class="nav-link" href="<?= base_url('/kontak') ?>">
      <i class="bi bi-envelope"></i> Kontak
    </a>
  </li>

</ul>
            </li><!-- End Produk Nav -->
        <?php
        }
        ?>
    </ul>

</aside>
<!-- End Sidebar-->