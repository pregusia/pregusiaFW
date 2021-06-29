#!/bin/bash

rm -f *.phar

./phar.php create ./framework.phar ./framework/
./phar.php create ./lib-api.phar ./lib-api/
./phar.php create ./lib-cache.phar ./lib-cache/
./phar.php create ./lib-cron.phar ./lib-cron/
./phar.php create ./lib-async.phar ./lib-async/
./phar.php create ./lib-datetime.phar ./lib-datetime/
./phar.php create ./lib-i18n.phar ./lib-i18n/
./phar.php create ./lib-orm.phar ./lib-orm/
./phar.php create ./lib-http.phar ./lib-http/
./phar.php create ./lib-sql-connector-mysqli.phar ./lib-sql-connector-mysqli/
./phar.php create ./lib-storage-filesystem.phar ./lib-storage-filesystem/
./phar.php create ./lib-storage-sql.phar ./lib-storage-sql/
./phar.php create ./lib-templating.phar ./lib-templating/
./phar.php create ./lib-utils.phar ./lib-utils/
./phar.php create ./lib-validation.phar ./lib-validation/
./phar.php create ./lib-web-core.phar ./lib-web-core/
./phar.php create ./lib-web-ui.phar ./lib-web-ui/
./phar.php create ./lib-auth.phar ./lib-auth/
./phar.php create ./lib-cli.phar ./lib-cli/
./phar.php create ./lib-content-renderer.phar ./lib-content-renderer/
./phar.php create ./lib-expression-parser.phar ./lib-expression-parser/
./phar.php create ./lib-block-language.phar ./lib-block-language/
./phar.php create ./lib-noting.phar ./lib-noting/
./phar.php create ./lib-mailer.phar ./lib-mailer/
./phar.php create ./lib-mailer-sendgrid.phar ./lib-mailer-sendgrid/
./phar.php create ./lib-jwt.phar ./lib-jwt/
./phar.php create ./lib-sourcecode-widget.phar ./lib-sourcecode-widget/

