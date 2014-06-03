# Database Codeigniter

Librarie codeigniter database connection

## Databases Supported

* Firebird/InterBase
* MySql
* MySqli
* PostGre
* ODBC
* MsSQL
* Sqlite
* oci8

## Usage

```
require_once './Database.php';

$mysql = new Database();
$mysql->connect('driver://username:password@hostname/database');

$query = $mysql->db->get('posts');

foreach ($query->result() as $row) {
    printf('<h1>%s</h1>', $row->title);
}
```

## Contributing

1. Fork it!
2. Create your feature branch: `git checkout -b my-new-feature`
3. Commit your changes: `git commit -am 'Add some feature'`
4. Push to the branch: `git push origin my-new-feature`
5. Submit a pull request :D

## Creator

**Rodolfo Silva**

+ [https://rodolfosilva.com](https://rodolfosilva.com)
+ [https://twitter.com/ro_dolfosilva](https://twitter.com/ro_dolfosilva)
+ [https://github.com/rodolfosilva](https://github.com/rodolfosilva)
