<?php $title = 'Chat'; ?>

<?php ob_start(); ?>
    <link rel="stylesheet" href="/assets/css/home.css">
<?php $templateCss = ob_get_clean(); ?>


<?php ob_start(); ?>
    <div class="discussions shaddow-sm">
        <header class="connected-user user d-flex px-3 py-4 align-items-center">
            <div class="user-icon mr-2">
                <img src="/assets/img/connected-user.png" alt="">
            </div>
            <div class="name font-weight-bold px-2"><?= $security->getInfos()['pseudo'] ?></div>
            <div class="ml-auto">
                <a class="btn btn-light" href="/logout">Logout</a>
            </div>
        </header>
        <?php foreach ($chats as $chat) { ?>
            <div class="discussion d-flex" data-id="<?= $chat['uuid'] ?>">
                <div class="user-icon">
                    <?php if ($chat['private'] == 1) { ?>
                        <img src="/assets/img/user.png" alt="">
                    <?php } else { ?>
                        <img src="/assets/img/group.png" alt="">
                    <?php } ?>
                </div>
                <div class="infos d-flex flex-column flex-grow-1 justify-content-between">
                    <div class="name font-weight-bold"><?= $chat[''] ?></div>
                    <div class="d-flex justify-content-between">
                        <div class="last-message text-muted">Last message on : <?= $chat['updated_on'] ?></div>
                        <div class="ml-auto unread">
                            <span class="badge badge-primary <?= $chat['unread'] == 0 ? 'd-none' : '' ?>"><?= $chat['unread'] ?></span>
                        </div>
                    </div>
                </div>
            </div>
        <?php } ?>
    </div>
    <div class="chat">

    </div>
<?php $templateContent = ob_get_clean(); ?>


<?php require('base.php'); ?>