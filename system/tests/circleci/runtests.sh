#!/usr/bin/env bash

cd system/tests

# We will increment this as we get bad statuses from RSpec and finally
# exit with that status at the end
STATUS=0

# Explode php_versions environment variable since we can't assign
# arrays in the YML
PHP_VERSIONS_ARRAY=(${php_versions// / })

# Slice the PHP versions array based on available parallelism
PHP_VERSIONS_ARRAY_SLICED=${PHP_VERSIONS_ARRAY[@]:$((CIRCLE_NODE_INDEX * CIRCLE_NODE_TOTAL)):$CIRCLE_NODE_TOTAL}

printf "Starting tests. Outputting results to build artifacts directory\n\n"

for PHPVERSION in ${PHP_VERSIONS_ARRAY_SLICED[@]}
do
	# Switch PHP version with phpenv and reload the Apache module
	printf "Testing under PHP ${PHPVERSION}\n\n"
	phpenv global $PHPVERSION
	echo "LoadModule php5_module /home/ubuntu/.phpenv/versions/${PHPVERSION}/libexec/apache2/libphp5.so" > /etc/apache2/mods-available/php5.load
	sudo service apache2 restart

	# We'll store our build artifacts under the name of the current PHP version
	mkdir -p $CIRCLE_ARTIFACTS/$PHPVERSION/

	pushd rspec
		# Run the tests, outputting the results in the artifacts directory.
		printf "Running Rspec tests\n\n"
		bundle exec rspec -c -fp -fh -o $CIRCLE_ARTIFACTS/$PHPVERSION/rspec.html

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
	pushd phpunit-new
		printf "Running PHPUnit tests\n\n"
		phpunit tests/ > $CIRCLE_ARTIFACTS/$PHPVERSION/phpunit.txt

		# Save our exit status code
		((STATUS+=$?))

		# Remove CLI colors
		sed -i -r "s/\x1B\[([0-9]{1,2}(;[0-9]{1,2})*)?m//g" $CIRCLE_ARTIFACTS/$PHPVERSION/phpunit.txt

	popd

done

exit $STATUS