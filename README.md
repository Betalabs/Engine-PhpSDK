# Engine-PhpSDK

<p align="center">
<img src="https://app.buddy.works/betalabs/engine-phpsdk/pipelines/pipeline/59764/badge.svg?token=7694a41867a494d5be5dd61a675f7e43fc18c053ab9c6091a392ce111cd03de5" alt="Buddy Status"/>
</p>

This package is a helper to integrate with Engine. The documentation for integration can be found <a href="https://betalabs.atlassian.net/wiki/spaces/APPS/overview" target="_blank">here</a>.

## Request

The `Betalabs\Engine\Request` class is responsible for initializing the request specific types objects. If you need to make a GET request call you can:

```php
$get = \Betalabs\Engine\Request::get();
$response = $get->send('path/to/api'); // ['data' => [...]]
$statusCode = $get->statusCode(); // 200
```

It's also possible to inject the `Betalabs\Engine\Request`:

```php
class Object {

  protected $request;
  
  public __construct(\Betalabs\Engine\Request $request)
  {
    $this->request = $request;
  }
  
  public function get()
  {
    $get = $this->request->get();
    $response = $get->send('path/to/api'); // ['data' => [...]]
    $statusCode = $get->statusCode(); // 200
  }

}
```
There are five methods possible: GET, POST, PUT, PATCH and DELETE. In all methods the first parameter is the API path. For POST, PUT, PATCH and DELETE the second parameter is the data to be sent to the API, it must be sent in an array. For instance:

```php
$post = \Betalabs\Engine\Request::post();
$post->send(
  'path/to/api',
  [
    'parameter1' => 'value 1',
    'parameter2' => 'value 2',
    // ...
  ]
);
```

### URL builder

By default the package always adds the `api` prefix to all URLs. In the previous example the URL will be (assuming `http://engine.url` is the endpoint): `http://engine.url/api/path/to/api`.

It is possible to change this behavior adding using `setEndpointSuffix()` method which accepts a `string` or `null`:

```php
$get->setEndpointSuffix(null)->send('path/to/api'); // http://engine.url/path/to/api
```

## Configuration file

Configuration file is expected to be stored in the main (root) directory of the project and shall be named `engine-sdk.xml`.

This is its basic format:
```xml
<?xml version="1.0" encoding="UTF-8"?>
<engine-sdk>
    <auth>
        <accessToken></accessToken>
    </auth>
    <environment>
        <env></env>
        <endpoint></endpoint>
    </environment>
    <routeProvider>
        <path></path>
        <class></class>
    </routeProvider>
    <permissionProvider>
        <path></path>
        <class></class>
    </permissionProvider>
    <migrationProvider>
        <path></path>
        <class></class>
    </migrationProvider>
    <genesisProvider>
        <path></path>
        <class></class>
    </genesisProvider>
</engine-sdk>
```

Each section of this document will relate to its configuration.

## Routes

All routes must be declared in one single file which implements `Betalabs\Engine\RouteProvider` interface. The `route` method receives a `Aura\Router\Map` parameter, its usage can be checked <a href="https://github.com/auraphp/Aura.Router/blob/3.x/docs/defining-routes.md" target="_blank">here</a>.

The location of route file is declared in configuration file:

```xml
<routeProvider>
    <path></path>
    <class></class>
</routeProvider>
```

Where `path` is the relative path to the file (based on the root directory) and `class` is the class name (with namespace if exists). The `path` is not required when the class is autoloaded.

### Engine requests

All requests to the App are dispatched by the Engine, these requests might be originated by an trigger or an authenticated user. However, for some applications, it might be useful to own some endpoints for loose requests (that are not directly dispatched by Engine).

Assume we are building an app that creates a tag for an order named <i>TagCreator</i>; there are two main starters:
(1) An Engine user is managing the orders and click in "Make tag";
(2) An external system wants to generate a tag.

In the first case Engine owns a trigger to dispatch an request to the app. In this request Engine will add some information to identify which order the user wants to generate the tag (such as the ID) and via Engine requests is possible to gather all information to response the request with the tag. The second case the app must own a route prepared to receive all information via request parameter and then generate the tag.

Note in the second case Engine does not take any action and is not used to generate any data. To make a request directly to the app dispatch to: `http://{app-company}-{app-repository}.engine.url/` where `{app-company}` and `{app-repository}` are GitHub's Company and Repository name (used to register app in Engine).

## Authentication

By default all requests are authenticated using stored token. It is possible to disable using `mustNotAuthorize` method:

```php
 $get = \Betalabs\Engine\Request::get();
 $response = $get
  ->mustNotAuthorize()
  ->send('path/to/api');
```

Of course is possible to enable using the `mustAuthorize()` method.

## Permissions

During the App boot process Engine asks for the permissions. There is an easy way to define them where you must create a class that implements `Betalabs\Engine\PermissionProvider`. This class must own a method that adds all permissions:

```php
public function permissions(\Betalabs\Engine\Permissions\Register $register)
{

    $register->add(new \Betalabs\Engine\Permissions\Permission(
        'permission-0-name',
        'Permission #0 name',
        'Permission #0 description'
    ));

    $register->add(new \Betalabs\Engine\Permissions\Permission(
        'permission-1-name',
        'Permission #1 name',
        'Permission #1 description'
    ));

}
```

The location of this file is declared in configuration file:

```xml
<permissionProvider>
    <path></path>
    <class></class>
</permissionProvider>
```

Where `path` is the relative path to the file (based on the root directory) and `class` is the class name (with namespace if exists). The `path` is not required when the class is autoloaded.

If this node does not exist or no permission is declared then an 404 HTTP code is returned to Engine when it asks for the permission.

By default the `boot/permission` route is automatically defined and treated by the SDK.

## Database migration

During the App boot process Engine starts migration process. You can create a class that implements `Betalabs\Engine\MigrationProvider`. This class must own a method that runs the migration:

```php
public function run()
{

    // Migration process

    return new \Betalabs\Engine\Requests\BootResponse(
        true,
        'Success!'
    );
    
}
```

It is necessary to return an `Betalabs\Engine\Requests\BootResponse` object, this way Engine will be able to log what happen during this process.

The location of this file is declared in configuration file:

```xml
<migrationProvider>
    <path></path>
    <class></class>
</migrationProvider>
```

Where `path` is the relative path to the file (based on the root directory) and `class` is the class name (with namespace if exists). The `path` is not required when the class is autoloaded.

If this node does not exist then SDK informs Engine no migration process is needed.

By default the `boot/database` route is automatically defined and treated by the SDK.

## Genesis boot

During process of associate an App with a tenant Engine boot Genesis process. You can create a class that implements `Betalabs\Engine\GenesisProvider`. This class must own a method that runs the migration:

```php
public function run()
{

    // Genesis process

    return new \Betalabs\Engine\Requests\BootResponse(
        true,
        'Success!'
    );
    
}
```

It is necessary to return an `Betalabs\Engine\Requests\BootResponse` object, this way Engine will be able to log what happen during this process.

The location of this file is declared in configuration file:

```xml
<genesisProvider>
    <path></path>
    <class></class>
</genesisProvider>
```

Where `path` is the relative path to the file (based on the root directory) and `class` is the class name (with namespace if exists). The `path` is not required when the class is autoloaded.

If this node does not exist then SDK informs Engine no genesis process is needed.

By default the `boot/genesis` route is automatically defined and treated by the SDK.

## Development

During development you might want to manually define tokens and endpoints. It is possible using configuration file.

### Endpoint

By default all requests are dispatched against production URL however you can change this behavior using `environment` section in configuration file.

```xml
<environment>
    <env></env>
    <endpoint></endpoint>
</environment>
```

`env` node can be filled with two values: `Sandbox` or `Production`; this way the endpoint URL will be automatically defined. Nevertheless if you fill `endpoint` node this value will be used as endpoint URL independently `env` node value.

`environment`, `env` and `endpoint` nodes are not required.

### Token

You might want to use a specific token to access Engine API. This can be done through `auth` section in configuration file:

```xml
<auth>
    <accessToken></accessToken>
</auth>
```

- `accessToken` is the access token string to be used in all requests

The `auth` node is not required, however to be used `accessToken` subnode, it must be declared.