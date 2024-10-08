# Deployer Cli

an cli application to integrate with a custom nodejs deployer build with laravel zero

## Installation

-   Download [deployer-cli.phar](https://github.com/ahmdadl/deployer-cli/raw/main/builds/deployer-cli) file and place in directory lets say C:\Common

-   add an alias in your terminal to this file
    alias deployer=php C:\Common\deployer-cli $\*

```
In These docs we will use deployer as an alias to deployer-cli
```

### Available command

-   init: Initialize deployer in current project

```bash
deployer app:init

NonInteractive:
    deployer app:init {app?} {alias?}

Options:
    {app? : name of the repo in deployer config file}
    {alias? : alias of the app name to be used locally}
```

-   apps: Manage Current added apps

```bash
deployer app:apps list


Args:
    {list: list all installed apps}
    {add: add new app}
    {edit: edit app}
    {delete: delete an app}
    {default: set app as default}

Options:
    {--app : name of the app in deployer config file (required)}
    {--alias= : alias of the app name to be used locally}
```

-   deploy: Deploy app to the deployer url

```bash
deployer app:deploy test-app

Args:
    {appName?: app name or alias name}

Options:
    {--default: directly push to default app}
```
