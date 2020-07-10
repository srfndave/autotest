<ul class="pagination justify-content-end">
    <?php
    $previous = (int)$page - 1;
    $disabled = ($previous == 0) ? "disabled" : "";
    echo "<li class=\"page-item $disabled\"><a class=\"page-link\" href=\"index.php?page=$previous\">Previous</a></li>";
    foreach($pages as $page_no) {
        $active = ($page_no == $page) ? "active" : "";
        echo "<li class=\"page-item $active\"><a class=\"page-link\" href=\"index.php?page=$page_no\">$page_no</a></li>";
    }
    $next = (int)$page + 1;
    $disabled = ($next > $page_no) ? "disabled" : "";
    echo "<li class=\"page-item $disabled\"><a class=\"page-link\" href=\"index.php?page=$next\">Next</a></li>";
    ?>
</ul>
