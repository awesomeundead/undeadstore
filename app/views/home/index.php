<?php $this->layout('layout', ['title' => 'Undead Store', 'session' => $session]) ?>
<nav>
    <?php $this->insert('home/nav') ?>
</nav>
<section hidden="hidden" id="carousel">
    <div class="side left">
        <button></button>
    </div>
    <div class="container"></div>
    <div class="side right">
        <button></button>
    </div>
</section>

<?php $this->insert('home/template') ?>
<script src="/scripts/library.js?release=5"></script>
<script src="/scripts/index.js?release=5"></script>