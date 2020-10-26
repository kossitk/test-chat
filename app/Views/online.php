<?php ob_start(); ?>
    <header class="connected-user user d-flex px-3 py-4 align-items-center mb-2">
        <div class="user-icon mr-2">
            <img src="/assets/img/online-user.png" alt="">
        </div>
        <h3 class="font-weight-light px-2">Online users</h3>
        <div class="ml-auto">
            <span class="badge badge-primary "><?= count($users) - 1 ?></span>
        </div>
    </header>
    <?php foreach ($users as $i => $user) {
        if ($user['uuid'] != $userUuid) {
        ?>
        <div class="online-user d-flex align-items-center" data-id="<?= $user['uuid'] ?>">
            <div class="user-icon">
                <img src="/assets/img/online-user.png" alt="">
            </div>
            <div class="infos d-flex flex-grow-1 justify-content-between align-items-center">
                <div class="name font-weight-bold text-blue-1 px-2"><?= $user['pseudo'] ?></div>
                <div class="dropdown dropleft">
                    <button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton<?= $i ?>" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        Actions
                    </button>
                    <div class="dropdown-menu" aria-labelledby="dropdownMenuButton<?= $i ?>">
                        <a class="dropdown-item" href="/private-chat?user=<?= $user['uuid'] ?>">Private chat</a>
                        <a class="dropdown-item disabled" href="#">Add to this group</a>
                    </div>
                </div>
            </div>
        </div>
    <?php } } ?>
<?php $templateFullContent = ob_get_clean(); ?>

