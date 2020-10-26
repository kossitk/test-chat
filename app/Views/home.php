<?php $title = 'Chat'; ?>

<?php ob_start(); ?>
    <link rel="stylesheet" href="/assets/css/home.css">
<?php $templateCss = ob_get_clean(); ?>


<?php ob_start(); ?>
    <div class="discussions shaddow-sm">
        <header class="connected-user user d-flex px-3 py-4 align-items-center" id="connectedUserBox" data-user="<?= $security->getInfos()['uuid'] ?>">
            <div class="user-icon mr-2">
                <img src="/assets/img/connected-user.png" alt="">
            </div>
            <div class="name font-weight-bold px-2"><?= $security->getInfos()['pseudo'] ?></div>
            <div class="ml-auto">
                <div class="dropdown">
                    <button class="btn btn-light dropdown-toggle" type="button" id="dropdownMainMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        Menu
                    </button>
                    <div class="dropdown-menu" aria-labelledby="dropdownMainMenuButton">
                        <a class="dropdown-item" id="viewOnlineUsers" href="/online">View online users</a>
                        <a class="dropdown-item" href="#" data-toggle="modal" data-target="#newGroupModal">Create group</a>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item" href="/logout">Logout</a>
                    </div>
                </div>

                <!-- Modal -->
                <div class="modal fade" id="newGroupModal" tabindex="-1" aria-labelledby="newGroupModalLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="newGroupModalLabel">New Group</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <form action="/create-group" id="newGroupForm" name="newGroupForm" method="post">
                                    <div class="form-group">
                                        <input type="text" name="name" id="name" max="55" tabindex="1" class="form-control" placeholder="Your Group name" value="">
                                        <div class="invalid-feedback"></div>
                                        <small class="form-text text-muted">Please provide a name for your group. Maximum characters : 55</small>
                                    </div>
                                </form>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                <button type="submit" form="newGroupForm" class="btn btn-primary">Save changes</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </header>
        <?php foreach ($chats as $chat) { ?>
            <div class="discussion d-flex" data-id="<?= $chat['uuid'] ?>" id="chat_<?= $chat['uuid'] ?>">
                <div class="user-icon">
                    <?php if ($chat['private'] == 1) { ?>
                        <img src="/assets/img/user.png" alt="">
                    <?php } else { ?>
                        <img src="/assets/img/group.png" alt="">
                    <?php } ?>
                </div>
                <div class="infos flex-grow-1 ml-3">
                    <div class="name font-weight-bold"><?= $chat['group_name'] ?></div>
                    <div class="d-flex justify-content-between small align-items-center">
                        <div class="last-message text-muted">Last message on : <?= $chat['updated_on'] ?></div>
                        <div class="ml-auto unread">
                            <span class="badge badge-primary <?= $chat['unread'] == 0 ? 'd-none' : '' ?>"><?= $chat['unread'] ?></span>
                        </div>
                    </div>
                </div>
            </div>
        <?php } ?>
    </div>
    <div class="chat position-relative p-4">
        <div class="flash-bag">
            <?php if (isset($_SESSION['flash'])) {
                foreach ($_SESSION['flash'] as $type => $flashMessage) { ?>
                <div class="alert alert-<?= $type ?> alert-dismissible fade show" role="alert">
                    <?= $flashMessage ?>
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            <?php } } unset($_SESSION['flash']); ?>
        </div>

        <div class="messages" id="messageContainer">
            <a href="#" id="loadPreviousMessages" class="align-self-center small my-4 d-none">Load older messages</a>
        </div>
        <div id="messageTemplate" class="d-none">
            <div class="message">
                <div class="d-flex justify-content-between text-muted small date-and-user">
                    <div class="user pr-2 mr-2 font-weight-bold"></div>
                    <div class="date pl-2 ml-2 align-self-end"></div>
                </div>
                <div class="message-content">
                </div>
            </div>
        </div>
        <form action="" class="form form-add-message" method="post" id="formAddMessage">
            <div class="d-flex align-items-center">
                <div class="form-group flex-grow-1 pr-2">
                    <label for="addMessageTextarea">Your message</label>
                    <textarea class="form-control" id="addMessageTextarea" rows="3" name="message"></textarea>
                </div>
                <button type="submit" class="btn btn-success">Save</button>
            </div>
        </form>
    </div>
    <div class="online-users d-none" id="onlineUsers">
    </div>
<?php $templateContent = ob_get_clean(); ?>

<?php ob_start(); ?>
    <script src="/assets/js/home.js"></script>
<?php $templateJavascript = ob_get_clean(); ?>


<?php require('base.php'); ?>