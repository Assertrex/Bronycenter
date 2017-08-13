<?php
// Get list of removed posts.
$removedPosts = $o_database->read(
    'p.id, p.user_id, p.datetime, p.like_count, p.comment_count, p.type, p.status, p.delete_id, p.delete_datetime, p.delete_reason, up.display_name, up.username, up.account_type, ud.display_name AS delete_displayname',
    'posts p',
    'INNER JOIN users up ON up.id = p.user_id INNER JOIN users ud ON ud.id = p.delete_id WHERE p.status != 0 ORDER BY p.delete_datetime DESC LIMIT 25',
    []
);
?>

<h1>Removed posts</h1>

<table class="table table-striped table-hover table-sm">
    <thead class="thead-inverse">
        <tr>
            <th class="text-center" style="width: 60px;">ID</th>
            <th>Display name</th>
            <th>Post published</th>
            <th>Deleted at</th>
            <th>Deleted by</th>
            <th>Delete reason</th>
            <th>Likes</th>
            <th>Comments</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php
        foreach($removedPosts as $post) {
            // Check if post was removed by author.
            $authorRemoved = $post['display_name'] === $post['delete_displayname'];

            // Display badge for administrators and moderators.
            switch ($post['account_type']) {
                case '0':
                    $userBadge = '<span class="d-inline-block badge badge-secondary mr-1" style="width: 70px;">Unverified</span>';
                    break;
                case '9':
                    $userBadge = '<span class="d-inline-block badge badge-danger mr-1" style="width: 70px;">Admin</span>';
                    break;
                case '8':
                    $userBadge = '<span class="d-inline-block badge badge-info mr-1" style="width: 70px;">Mod</span>';
                    break;
                default:
                    $userBadge = '';
            }
        ?>

        <?php echo $authorRemoved ? '<tr style="opacity: .7;">' : '<tr>'; ?>
            <th class="text-center" scope="row"><?php echo $post['id']; ?></th>
            <td><?php echo $userBadge; ?> <?php echo $post['display_name']; ?> (@<?php echo $post['username']; ?>)</td>
            <td><?php echo $o_system->getDateIntervalString($o_system->countDateInterval($post['datetime'])); ?></td>
            <td><?php echo $o_system->getDateIntervalString($o_system->countDateInterval($post['delete_datetime'])); ?></td>
            <td><?php echo $post['delete_displayname']; ?></td>
            <td><?php echo $post['delete_reason'] ?? '<small class="text-muted">No reason</small>'; ?></td>
            <td><?php echo $post['like_count']; ?></td>
            <td><?php echo $post['comment_count']; ?></td>
            <td></td>
        </tr>

        <?php
        } // foreach
        ?>
    </tbody>
</table>
