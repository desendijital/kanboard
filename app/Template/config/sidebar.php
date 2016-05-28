<div class="sidebar">
    <h2><?= t('Actions') ?></h2>
    <ul>
        <li <?= $this->app->checkMenuSelection('ConfigController', 'index') ?>>
            <?= $this->url->link(t('About'), 'ConfigController', 'index') ?>
        </li>
        <li <?= $this->app->checkMenuSelection('ConfigController', 'application') ?>>
            <?= $this->url->link(t('Application settings'), 'ConfigController', 'application') ?>
        </li>
        <li <?= $this->app->checkMenuSelection('ConfigController', 'project') ?>>
            <?= $this->url->link(t('Project settings'), 'ConfigController', 'project') ?>
        </li>
        <li <?= $this->app->checkMenuSelection('ConfigController', 'board') ?>>
            <?= $this->url->link(t('Board settings'), 'ConfigController', 'board') ?>
        </li>
        <li <?= $this->app->checkMenuSelection('ConfigController', 'calendar') ?>>
            <?= $this->url->link(t('Calendar settings'), 'ConfigController', 'calendar') ?>
        </li>
        <li <?= $this->app->checkMenuSelection('link') ?>>
            <?= $this->url->link(t('Link settings'), 'link', 'index') ?>
        </li>
        <li <?= $this->app->checkMenuSelection('CurrencyController', 'index') ?>>
            <?= $this->url->link(t('Currency rates'), 'CurrencyController', 'index') ?>
        </li>
        <li <?= $this->app->checkMenuSelection('ConfigController', 'integrations') ?>>
            <?= $this->url->link(t('Integrations'), 'ConfigController', 'integrations') ?>
        </li>
        <li <?= $this->app->checkMenuSelection('ConfigController', 'webhook') ?>>
            <?= $this->url->link(t('Webhooks'), 'ConfigController', 'webhook') ?>
        </li>
        <li <?= $this->app->checkMenuSelection('ConfigController', 'api') ?>>
            <?= $this->url->link(t('API'), 'ConfigController', 'api') ?>
        </li>
        <?= $this->hook->render('template:config:sidebar') ?>
    </ul>
</div>
