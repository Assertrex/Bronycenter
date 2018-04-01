<?php

// Define variables for pagination settings
$currentPage = intval($_GET['p'] ?? 1);
$availablePages = intval(ceil($postsAmount / $fetchAmount));
$currentFirstpage = false;
$nextAvailable = false;

// Check if page number is higher than available pages
if ($currentPage > $availablePages) {
    $currentPage = $availablePages;
}

// Check if page number is equal or lower than 1 and set it as first page
if ($currentPage <= 1) {
    $currentPage = 1;
    $currentFirstpage = true;
}

// Check if there are is next page available
if (($availablePages - $currentPage) > 0) {
    $nextAvailable = true;
}

// Define variables for numbers removing
$leftRemoved = false;
$rightRemoved = false;
?>

<nav aria-label="Posts pages pagination">
    <ul class="pagination pagination-sm justify-content-center mb-0">
        <li class="page-item<?php echo $currentFirstpage ? ' disabled' : ''; ?>">
            <a class="page-link" href="?p=<?php echo $currentPage - 1; ?>"><?= $o_translation->getString('common', 'prev') ?></a>
        </li>

        <?php
        for ($i = 1; $i < $availablePages + 1; $i++) {
            // Check if current page is active
            $currentActive = false;
            if ($currentPage === $i) {
                $currentActive = true;
            }

            $displayNumber = true;

            if ($i > 1 && $i < $currentPage - 1 && $availablePages != 7) {
                if ($i < $availablePages - 4 && $currentPage != 4) {
                    $displayNumber = false;

                    if (!$leftRemoved) {
                        $leftRemoved = true;

                        echo '<li class="page-item disabled">';
                            echo '<a class="page-link">...</a>';
                        echo '</li>';
                    }
                }
            }

            if ($i < $availablePages && $i > $currentPage + 1 && $availablePages != 7) {
                if ($i > 5 && $currentPage != $availablePages - 3) {
                    $displayNumber = false;

                    if (!$rightRemoved) {
                        $rightRemoved = true;

                        echo '<li class="page-item disabled">';
                            echo '<a class="page-link">...</a>';
                        echo '</li>';
                    }
                }
            }

            if ($displayNumber) {
        ?>

        <li class="page-item <?php echo $currentActive ? ' active' : ''; ?>">
            <a class="page-link" href="?p=<?php echo $i; ?>"><?php echo $i; ?></a>
        </li>

        <?php
            } // if
        } // for
        ?>

        <li class="page-item<?php echo $nextAvailable ? '' : ' disabled'; ?>">
            <a class="page-link" href="?p=<?php echo $currentPage + 1; ?>"><?= $o_translation->getString('common', 'next') ?></a>
        </li>
    </ul>
</nav>
