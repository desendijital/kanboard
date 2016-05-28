<?php

namespace Kanboard\ServiceProvider;

use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Kanboard\Core\Security\AuthenticationManager;
use Kanboard\Core\Security\AccessMap;
use Kanboard\Core\Security\Authorization;
use Kanboard\Core\Security\Role;
use Kanboard\Auth\RememberMeAuth;
use Kanboard\Auth\DatabaseAuth;
use Kanboard\Auth\LdapAuth;
use Kanboard\Auth\TotpAuth;
use Kanboard\Auth\ReverseProxyAuth;

/**
 * Authentication Provider
 *
 * @package Kanboard\ServiceProvider
 * @author  Frederic Guillot
 */
class AuthenticationProvider implements ServiceProviderInterface
{
    /**
     * Register providers
     *
     * @access public
     * @param  \Pimple\Container $container
     * @return \Pimple\Container
     */
    public function register(Container $container)
    {
        $container['authenticationManager'] = new AuthenticationManager($container);
        $container['authenticationManager']->register(new TotpAuth($container));
        $container['authenticationManager']->register(new RememberMeAuth($container));
        $container['authenticationManager']->register(new DatabaseAuth($container));

        if (REVERSE_PROXY_AUTH) {
            $container['authenticationManager']->register(new ReverseProxyAuth($container));
        }

        if (LDAP_AUTH) {
            $container['authenticationManager']->register(new LdapAuth($container));
        }

        $container['projectAccessMap'] = $this->getProjectAccessMap();
        $container['applicationAccessMap'] = $this->getApplicationAccessMap();

        $container['projectAuthorization'] = new Authorization($container['projectAccessMap']);
        $container['applicationAuthorization'] = new Authorization($container['applicationAccessMap']);

        return $container;
    }

    /**
     * Get ACL for projects
     *
     * @access public
     * @return AccessMap
     */
    public function getProjectAccessMap()
    {
        $acl = new AccessMap;
        $acl->setDefaultRole(Role::PROJECT_VIEWER);
        $acl->setRoleHierarchy(Role::PROJECT_MANAGER, array(Role::PROJECT_MEMBER, Role::PROJECT_VIEWER));
        $acl->setRoleHierarchy(Role::PROJECT_MEMBER, array(Role::PROJECT_VIEWER));

        $acl->add('Action', '*', Role::PROJECT_MANAGER);
        $acl->add('ActionProject', '*', Role::PROJECT_MANAGER);
        $acl->add('ActionCreation', '*', Role::PROJECT_MANAGER);
        $acl->add('Analytic', '*', Role::PROJECT_MANAGER);
        $acl->add('Board', 'save', Role::PROJECT_MEMBER);
        $acl->add('BoardPopover', '*', Role::PROJECT_MEMBER);
        $acl->add('TaskPopoverController', '*', Role::PROJECT_MEMBER);
        $acl->add('Calendar', 'save', Role::PROJECT_MEMBER);
        $acl->add('Category', '*', Role::PROJECT_MANAGER);
        $acl->add('Column', '*', Role::PROJECT_MANAGER);
        $acl->add('Comment', '*', Role::PROJECT_MEMBER);
        $acl->add('Customfilter', '*', Role::PROJECT_MEMBER);
        $acl->add('Export', '*', Role::PROJECT_MANAGER);
        $acl->add('TaskFile', array('screenshot', 'create', 'save', 'remove', 'confirm'), Role::PROJECT_MEMBER);
        $acl->add('Gantt', '*', Role::PROJECT_MANAGER);
        $acl->add('ProjectViewController', array('share', 'updateSharing', 'integrations', 'updateIntegrations', 'notifications', 'updateNotifications', 'duplicate', 'doDuplication'), Role::PROJECT_MANAGER);
        $acl->add('ProjectPermissionController', '*', Role::PROJECT_MANAGER);
        $acl->add('ProjectEditController', '*', Role::PROJECT_MANAGER);
        $acl->add('ProjectFile', '*', Role::PROJECT_MEMBER);
        $acl->add('Projectuser', '*', Role::PROJECT_MANAGER);
        $acl->add('ProjectStatusController', '*', Role::PROJECT_MANAGER);
        $acl->add('SubtaskController', '*', Role::PROJECT_MEMBER);
        $acl->add('SubtaskRestrictionController', '*', Role::PROJECT_MEMBER);
        $acl->add('SubtaskStatusController', '*', Role::PROJECT_MEMBER);
        $acl->add('Swimlane', '*', Role::PROJECT_MANAGER);
        $acl->add('TaskViewController', 'remove', Role::PROJECT_MEMBER);
        $acl->add('TaskCreationController', '*', Role::PROJECT_MEMBER);
        $acl->add('TaskBulkController', '*', Role::PROJECT_MEMBER);
        $acl->add('Taskduplication', '*', Role::PROJECT_MEMBER);
        $acl->add('TaskRecurrenceController', '*', Role::PROJECT_MEMBER);
        $acl->add('TaskImportController', '*', Role::PROJECT_MANAGER);
        $acl->add('TaskInternalLinkController', '*', Role::PROJECT_MEMBER);
        $acl->add('TaskExternalLink', '*', Role::PROJECT_MEMBER);
        $acl->add('TaskModificationController', '*', Role::PROJECT_MEMBER);
        $acl->add('TaskStatusController', '*', Role::PROJECT_MEMBER);
        $acl->add('UserAjaxController', array('mention'), Role::PROJECT_MEMBER);

        return $acl;
    }

    /**
     * Get ACL for the application
     *
     * @access public
     * @return AccessMap
     */
    public function getApplicationAccessMap()
    {
        $acl = new AccessMap;
        $acl->setDefaultRole(Role::APP_USER);
        $acl->setRoleHierarchy(Role::APP_ADMIN, array(Role::APP_MANAGER, Role::APP_USER, Role::APP_PUBLIC));
        $acl->setRoleHierarchy(Role::APP_MANAGER, array(Role::APP_USER, Role::APP_PUBLIC));
        $acl->setRoleHierarchy(Role::APP_USER, array(Role::APP_PUBLIC));

        $acl->add('Auth', array('login', 'check'), Role::APP_PUBLIC);
        $acl->add('CaptchaController', '*', Role::APP_PUBLIC);
        $acl->add('PasswordReset', '*', Role::APP_PUBLIC);
        $acl->add('Webhook', '*', Role::APP_PUBLIC);
        $acl->add('TaskViewController', 'readonly', Role::APP_PUBLIC);
        $acl->add('Board', 'readonly', Role::APP_PUBLIC);
        $acl->add('ICalendarController', '*', Role::APP_PUBLIC);
        $acl->add('FeedController', '*', Role::APP_PUBLIC);
        $acl->add('AvatarFileController', 'show', Role::APP_PUBLIC);

        $acl->add('ConfigController', '*', Role::APP_ADMIN);
        $acl->add('PluginController', '*', Role::APP_ADMIN);
        $acl->add('CurrencyController', '*', Role::APP_ADMIN);
        $acl->add('Gantt', array('projects', 'saveProjectDate'), Role::APP_MANAGER);
        $acl->add('GroupListController', '*', Role::APP_ADMIN);
        $acl->add('GroupCreationController', '*', Role::APP_ADMIN);
        $acl->add('GroupModificationController', '*', Role::APP_ADMIN);
        $acl->add('Link', '*', Role::APP_ADMIN);
        $acl->add('ProjectCreation', 'create', Role::APP_MANAGER);
        $acl->add('Projectuser', '*', Role::APP_MANAGER);
        $acl->add('TwoFactorController', 'disable', Role::APP_ADMIN);
        $acl->add('UserImportController', '*', Role::APP_ADMIN);
        $acl->add('UserCreationController', '*', Role::APP_ADMIN);
        $acl->add('UserListController', '*', Role::APP_ADMIN);
        $acl->add('UserStatusController', '*', Role::APP_ADMIN);
        $acl->add('UserCredentialController', array('changeAuthentication', 'saveAuthentication'), Role::APP_ADMIN);

        return $acl;
    }
}
