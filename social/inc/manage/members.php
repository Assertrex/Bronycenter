<?php
// Get list of newest members.
$members = $o_database->read(
    'id, display_name, username, registration_datetime, login_datetime, last_online, avatar, account_type, account_standing',
    'users',
    'ORDER BY id DESC',
    []
);
?>

<h1 class="text-center my-5">Members</h1>

<table class="table table-striped table-hover table-sm">
    <thead>
        <tr>
            <th class="text-center" style="width: 60px;">ID</th>
            <th>Display name &amp; username</th>
            <th>Member since</th>
            <th>Last login</th>
            <th>Last seen</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php
        foreach($members as $member) {
            // Display badge for administrators and moderators.
            switch ($member['account_type']) {
                case '0':
                    $userBadge = '<span class="d-inline-block badge badge-default mr-1" style="width: 70px;">Unverified</span>';
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

        <tr<?php if ($member['account_type'] == '0') { echo ' style="opacity: .7;"'; } ?>>
            <th class="text-center" scope="row"><?php echo $member['id']; ?></th>
            <td><?php echo $userBadge; ?> <?php echo $member['display_name']; ?> (@<?php echo $member['username']; ?>)</td>
            <td><?php echo $o_system->getDateIntervalString($o_system->countDateInterval($member['registration_datetime'])); ?></td>
            <td><?php echo $o_system->getDateIntervalString($o_system->countDateInterval($member['login_datetime'])); ?></td>
            <td><?php echo $o_system->getDateIntervalString($o_system->countDateInterval($member['last_online'])); ?></td>
            <td></td>
        </tr>

        <?php
        } // foreach
        ?>
    </tbody>
</table>
