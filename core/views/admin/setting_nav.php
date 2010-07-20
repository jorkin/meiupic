<ul>
    <li <?php if($res->get('setting_nav') == 'index'): ?>class="current"<?php endif; ?>><a href="admin.php?ctl=setting">常规设置</a></li>
    <li <?php if($res->get('setting_nav') == 'operator'): ?>class="current"<?php endif; ?>><a href="admin.php?ctl=setting&act=operator">管理员设置</a></li>
    <li <?php if($res->get('setting_nav') == 'password'): ?>class="current"<?php endif; ?>><a href="admin.php?ctl=setting&act=password">修改密码</a></li>
    <li <?php if($res->get('setting_nav') == 'task'): ?>class="current"<?php endif; ?>><a href="admin.php?ctl=setting&act=task">后台任务</a></li>
</ul>