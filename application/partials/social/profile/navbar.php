<?php if ($profileDetails['filled_about'] || $profileDetails['filled_fandom'] || $profileDetails['filled_creations']) { ?>
<ul class="nav nav-tabs" id="aside-tabs">
    <?php if ($profileDetails['filled_about']) { ?>
    <li class="nav-item">
        <a class="nav-link" id="aside-tab-about" href="#about"><?= $o_translation->getString('profile', 'about') ?></a>
    </li>
    <?php } ?>

    <li class="nav-item">
        <a class="nav-link active" id="aside-tab-posts" href="#posts"><?= $o_translation->getString('profile', 'posts') ?></a>
    </li>

    <?php if ($profileDetails['filled_fandom']) { ?>
    <li class="nav-item">
        <a class="nav-link" id="aside-tab-fandom" href="#fandom"><?= $o_translation->getString('profile', 'fandom') ?></a>
    </li>
    <?php } ?>

    <?php if ($profileDetails['filled_creations']) { ?>
    <li class="nav-item">
        <a class="nav-link" id="aside-tab-creations" href="#creations"><?= $o_translation->getString('profile', 'creations') ?></a>
    </li>
    <?php } ?>
</ul>
<?php } ?>
