<?php if ($session->isAuth()) : ?>
[nav depth="1" show_root_as_child="true" exclude="user/login,user/signup"]
<?php else : ?>
[nav depth="1" show_root_as_child="true" exclude="user/logout"]
<?php endif; ?>
