<?php

namespace EllisLab\ExpressionEngine\Service\Filesystem;

/**
 * ExpressionEngine - by EllisLab
 *
 * @package		ExpressionEngine
 * @author		EllisLab Dev Team
 * @copyright	Copyright (c) 2003 - 2014, EllisLab, Inc.
 * @license		http://ellislab.com/expressionengine/user-guide/license.html
 * @link		http://ellislab.com
 * @since		Version 3.0
 * @filesource
 */

// ------------------------------------------------------------------------

/**
 * ExpressionEngine File Service
 *
 * @package		ExpressionEngine
 * @subpackage	Event
 * @category	Service
 * @author		EllisLab Dev Team
 * @link		http://ellislab.com
 */
class Filesystem {

	/**
	 * Read a file from disk
	 *
	 * @param String $path File to read
	 * @return String File contents
	 */
	public function read($path)
	{
		if ( ! $this->exists($path))
		{
			throw new \Exception("File not found: {$path}");
		}
		elseif ( ! $this->isFile($path))
		{
			throw new \Exception("Not a file: {$path}");
		}
		elseif ( ! $this->isReadable($path))
		{
			throw new \Exception("Cannot read file: {$path}");
		}

		return file_get_contents($path);
	}

	/**
	 * Write a file to disk
	 *
	 * @param String $path File to write to
	 * @param String $data Data to write
	 * @param bool $overwrite Overwrite existing files?
	 */
	public function write($path, $data, $overwrite = FALSE)
	{
		if ($this->isDir($path))
		{
			throw new \Exception("Cannot write file, path is a directory: {$path}");
		}
		elseif ($this->isFile($path) && $overwrite == FALSE)
		{
			throw new \Exception("File already exists: {$path}");
		}

		file_put_contents($path, $data);

		$this->ensureCorrectAccessMode($path);
	}

	/**
	 * Make a new directory
	 *
	 * @param String $path Directory to create
	 * @param bool $with_index Add EE's default index.html file in the new dir?
	 */
	public function mkDir($path, $with_index = TRUE)
	{
		mkdir($path, DIR_WRITE_MODE);

		if ($with_index)
		{
			$this->addIndexHtml($path);
		}

		$this->ensureCorrectAccessMode($path);
	}

	/**
	 * Delete a file or directory
	 *
	 * @param String $path File or directory to delete
	 */
	public function delete($path)
	{
		if ($this->isDir($path))
		{
			return $this->deleteDir($path);
		}

		return $this->deleteFile($path);
	}

	/**
	 * Delete a file
	 *
	 * @param String $path File to delete
	 */
	public function deleteFile($path)
	{
		if ( ! $this->isFile($path))
		{
			throw new \Exception("File does not exist {$path}");
		}

		return @unlink($path);
	}

	/**
	 * Delete a directory
	 *
	 * @param String $path Directory to delete
	 * @param bool $leave_empty Keep the empty root directory?
	 */
	public function deleteDir($path, $leave_empty = FALSE)
	{
		$path = rtrim($path, '/');

		if ( ! $this->isDir($path))
		{
			throw new \Exception("Directory does not exist {$path}.");
		}

		if ($this->attemptFastDelete($path))
		{
			return TRUE;
		}

		$contents = new FilesystemIterator($path);

		foreach ($contents as $item)
		{
			if ($item->isDir())
			{
				$this->deleteDir($item->getPathname());
			}
			else
			{
				$this->deleteFile($item->getPathName());
			}
		}

		if ( ! $leave_empty)
		{
			@rmdir($path);
		}

		return TRUE;
	}

	/**
	 * Empty a directory
	 *
	 * @param String $path Directory to empty
	 * @param bool $add_index Add EE's default index.html file to the directory
	 */
	public function emptyDir($path, $add_index = TRUE)
	{
		$this->deleteDir($path, TRUE);
		$this->addIndexHtml($path);
	}

	/**
	 * Attempt to delete a file using the OS method
	 *
	 * We can't always do this, but it's much, much faster than iterating
	 * over directories with many children.
	 *
	 * @param String $path
	 */
	protected function attemptFastDelete($path)
	{
		$path_delete = $path.'_delete_'.mt_rand();

		@exec("mv {$path} {$path_delete}", $out, $ret);

		if (isset($ret) && $ret == 0)
		{
			@exec("rm -r -f {$path_delete}");
			return TRUE;
		}

		return FALSE;
	}

	/**
	 * Rename a file or directory
	 *
	 * @param String $source File or directory to rename
	 * @param String $dest New location for the file or directory
	 */
	public function rename($source, $dest)
	{
		if ( ! $this->exists($source))
		{
			throw new \Exception("Cannot rename non-existent path: {$source}");
		}
		elseif ($this->exists($dest))
		{
			throw new \Exception("Cannot rename, destination already exists: {$dest}");
		}

		rename($source, $dest);
		$this->ensureCorrectAccessMode($dest);
	}

	/**
	 * Copy a file or directory
	 *
	 * @param String $source File or directory to copy
	 * @param Stirng $dest Path to the duplicate
	 */
	public function copy($source, $dest)
	{
		if ( ! $this->exists($source))
		{
			throw new \Exception("Cannot copy non-existent path: {$source}");
		}

		copy($source, $dest);
		$this->ensureCorrectAccessMode($dest);
	}

	/**
	 * Get the filename and extension
	 *
	 * @param String $path Path to extract basename from
	 * @return String Filename with extension
	 */
	public function basename($path)
	{
		return pathinfo($path, PATHINFO_BASENAME);
	}

	/**
	 * Get the filename without extension
	 *
	 * @param String $path Path to extract filename from
	 * @return String Filename without extension
	 */
	public function filename($path)
	{
		return pathinfo($path, PATHINFO_FILENAME);
	}

	/**
	 * Get the extension
	 *
	 * @param String $path Path to extract extension from
	 * @return String Extension
	 */
	public function extension($path)
	{
		return pathinfo($path, PATHINFO_EXTENSION);
	}

	/**
	 * Check if a path exists
	 *
	 * @param String $path Path to check
	 * @return bool Path exists?
	 */
	public function exists($path)
	{
		return file_exists($path);
	}

	/**
	 * Check if a given path is a directory
	 *
	 * @param String $path Path to check
	 * @return bool Is a directory?
	 */
	public function isDir($path)
	{
		return is_dir($path);
	}

	/**
	 * Check if a given path is a file
	 *
	 * @param String $path Path to check
	 * @return bool Is a file?
	 */
	public function isFile($path)
	{
		return is_file($path);
	}

	/**
	 * Check if a path is readable
	 *
	 * @param String $path Path to check
	 * @return bool Is readable?
	 */
	public function isReadable($path)
	{
		return is_readable($path);
	}

	/**
	 * Check if a file or directory is writable
	 *
	 * Does some extra checks for safe_mode windows servers. Yuck.
	 *
	 * @param String $path Path to check
	 * @return bool Is writeable?
	 */
	public function isWriteable($path)
	{
		// If we're on a Unix server with safe_mode off we call is_writable
		if (DIRECTORY_SEPARATOR == '/' AND @ini_get("safe_mode") == FALSE)
		{
			return is_writable($file);
		}

		// For windows servers and safe_mode "on" installations we'll actually
		// write a file then read it.  Bah...
		if ($this->isDir($file))
		{
			$file = rtrim($file, '/').'/'.md5(mt_rand(1,100).mt_rand(1,100));

			if (($fp = @fopen($file, FOPEN_WRITE_CREATE)) === FALSE)
			{
				return FALSE;
			}

			fclose($fp);
			@chmod($file, DIR_WRITE_MODE);
			@unlink($file);
			return TRUE;
		}
		elseif (($fp = @fopen($file, FOPEN_WRITE_CREATE)) === FALSE)
		{
			return FALSE;
		}

		fclose($fp);
		return TRUE;
	}

	/**
	 * Add EE's default index file to a directory
	 */
	protected function addIndexHtml($dir)
	{
		$dir = rtrim($dir, '/');

		if ( ! $this->isDir($dir))
		{
			throw new \Exception("Cannot add index file to non-existant directory: {$dir}");
		}

		if ( ! $this->isFile($dir.'/index.html'))
		{
			$this->write($dir.'/index.html', 'Directory access is forbidden.');
		}
	}

	/**
	 * Writing files and directories should respect the write modes
	 * specified. Otherwise on some crudy hosts you end up unable
	 * to change those files via FTP.
	 *
	 * @param String $path Path to ensure access to
	 */
	protected function ensureCorrectAccessMode($path)
	{
		if ($this->isDir($path))
		{
			@chmod($dest, DIR_WRITE_MODE);
		}
		else
		{
			@chmod($dest, FILE_WRITE_MODE);
		}
	}
}