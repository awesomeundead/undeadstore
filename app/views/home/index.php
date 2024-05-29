<?php $this->layout('layout', ['title' => 'Undead Store', 'session' => $session]) ?>
<nav>
    <?php $this->insert('home/nav') ?>
    <?php $this->insert('home/banner') ?>
</nav>
<div hidden="hidden" id="expanded">
    <div class="image">
        <img alt="" src="/styles/loading.svg" />
    </div>
</div>
<section id="carousel">
    <div class="side left">
        <button></button>
    </div>
    <div class="container"></div>
    <div class="side right">
        <button></button>
    </div>
</section>
<?php $this->insert('home/template') ?>
<script src="/scripts/library.js?release=4"></script>
<script src="/scripts/index.js?release=4"></script>