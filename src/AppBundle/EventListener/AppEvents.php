<?php

namespace AppBundle\EventListener;

/**
 * Class AppEvent
 */
class AppEvents
{
    const APP_USER_CREATE = 'app_user.create';

    const APP_USER_UPDATE = 'app_user.update';

    const APP_USER_DELETE = 'app_user.delete';

    const REMOTE_USER_CREATE = 'remote_user.create';

    const REMOTE_USER_UPDATE = 'remote_user.update';

    const REMOTE_USER_MAIL_SENT = 'remote_user.mail_sent';

    const REMOTE_USER_VALIDATE_ONEKEY = 'remote_user.validate_onekey';
    
    const REMOTE_USER_INVALIDATE_ONEKEY = 'remote_user.invalidate_onekey';

}
