#!/usr/bin/env bash

# We will increment this as we get bad statuses from RSpec and finally
# exit with that status at the end
STATUS=0

# Explode php_versions environment variable since we can't assign
# arrays in the YML
PHP_VERSIONS_ARRAY=(${php_versions// / })

printf "Starting tests. Outputting results to build artifacts directory\n\n"

i=0
for PHPVERSION in ${PHP_VERSIONS_ARRAY[@]}
do
	if [ $(($i % $CIRCLE_NODE_TOTAL)) -eq $CIRCLE_NODE_INDEX ]
	then

		# Install MySQL 5.7 when we're testing PHP 7
		PHP_VERSION_ASPLODE=(${PHPVERSION//./ })
		PHP_MAJOR_VERSION=${PHP_VERSION_ASPLODE[0]}
		if [[ $PHP_MAJOR_VERSION -eq 7 ]]
		then
			# Script provided by CircleCI
			curl -sSL https://s3.amazonaws.com/circle-downloads/install-mysql5.7-circleci.sh | sh

			# Prevent "MySQL server has gone away" error
			echo -e "[mysqld]\nmax_allowed_packet=256M\nwait_timeout=300\ninteractive_timeout=300" | sudo sh -c "cat >> /etc/mysql/my.cnf"

			# Upgrade databases
			sudo mysql_upgrade -u ubuntu --force

			sudo service mysql restart
		fi

		# Switch PHP version with phpenv and reload the Apache module
		printf "Testing under PHP ${PHPVERSION}\n\n"
		phpenv global $PHPVERSION
		echo "LoadModule php${PHP_MAJOR_VERSION}_module /home/ubuntu/.phpenv/versions/${PHPVERSION}/libexec/apache2/libphp${PHP_MAJOR_VERSION}.so" > /etc/apache2/mods-available/php5.load

		# Disable opcode cache
		echo -e "\n[opcache]\nopcache.enable=0" | sudo sh -c "cat >> /home/ubuntu/.phpenv/versions/${PHPVERSION}/etc/php.ini"

		sudo service apache2 restart

		# We'll store our build artifacts under the name of the current PHP version
		mkdir -p $CIRCLE_ARTIFACTS/$PHPVERSION/

		pushd tests/rspec
			# Run the tests, outputting the results in the artifacts directory.
			printf "Running Rspec tests\n\n"
			bundle exec rspec -c -fd -fh -o $CIRCLE_ARTIFACTS/$PHPVERSION/rspec.html tests/**/*.rb

			# Append status code for this test
			((STATUS+=$?))

			# If screenshots were taken, move them to the build artifacts directory
			if [ -d "./screenshots" ]; then
				printf "Screenshots taken, moved to build artifacts directory\n\n"
				mv screenshots/* $CIRCLE_ARTIFACTS/$PHPVERSION/
				rmdir screenshots
			fi
		popd

		# PHPUnit tests
		pushd system/ee/EllisLab/Tests/
			printf "Running PHPUnit tests\n\n"
			phpunit ExpressionEngine/ > $CIRCLE_ARTIFACTS/$PHPVERSION/phpunit.txt

			# Save our exit status code
			((STATUS+=$?))

			# Remove CLI colors
			sed -i -r "s/\x1B\[([0-9]{1,2}(;[0-9]{1,2})*)?m//g" $CIRCLE_ARTIFACTS/$PHPVERSION/phpunit.txt

		popd
	fi
	((i++))
done

exit $STATUS
