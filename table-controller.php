<?php
if ( ! class_exists( 'WP_List_table' ) ) {
	require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

class String_Locator_Table extends WP_List_table {
	function set_items( $items ) {
		$this->items = $items;
	}

	function get_columns() {
		$columns = array(
			'stringresult' => __( 'String', 'string-locator' ),
			'filename'     => __( 'File', 'string-locator' ),
			'linenum'      => __( 'Line number', 'string-locator' )
		);

		return $columns;
	}

	function prepare_items() {
		$columns               = $this->get_columns();
		$hidden                = array();
		$sortable              = array();

		$this->_column_headers = array( $columns, $hidden, $sortable );
	}

	function no_items() {
		_e( 'Your string was not present in any of the available files.', 'string-locator' );
	}

	function single_row( $item ) {
		static $row_class = '';
		$row_class = ( $row_class == '' ? ' class="alternate"' : '' );

		echo '<tr' . $row_class . '>';
		if ( isset( $item['header'] ) && ! empty( $item['header'] ) ) {
			$this->single_row_sub_header( $item );
		}
		else {
			$this->single_row_columns( $item );
		}

		echo '</tr>';
	}

	function single_row_sub_header( $item ) {
		list( $columns, $hidden ) = $this->get_column_info();

		$class = 'class=" column-$column_name"';

		$style = '';
		if ( in_array( '', $hidden ) ) {
			$style = ' style="display:none;"';
		}

		$attributes = "$class$style";
		$column_count = count( $columns );

		printf(
			'<th colspan="%d" %s"><h3>%s</h3></th>',
			$column_count,
			$attributes,
			$item['header']
		);
	}

	function delete_file_url($file_url) {
		$url = ('tools.php?page=string-locator');
		if (false === ($creds = request_filesystem_credentials($url, '', false, false, null) ) ) {
			return; // stop processing here
		}
		if ( ! WP_Filesystem($creds) ) {
			request_filesystem_credentials($url, '', true, false, null);
			return;
		}
		global $wp_filesystem;
		if ( ! $wp_filesystem->delete( $file_url ) ) {
		    echo 'error saving file!';
		    error_log('error deleting file');
		}
		error_log('file deleted');
		//return 'z';
	}

	function column_stringresult( $item ) {
		
		error_log(print_r($item, 1));

		$actions = array(
			'edit' => sprintf(
				'<a href="%s">%s</a>',
				esc_url( $item['editurl'] ),
				__( 'Edit' )
			)/*,
			'delete' => sprintf(
				'<a href="%s">%s</a>',
				//esc_url( $item['deleteurl'] ),
				$this->delete_file_url( $item['xurl'] ),
				__( 'Delete' )
			)*/
		);

		return sprintf(
			'%s %s',
			$item['stringresult'],
			$this->row_actions( $actions )
		);
	}

	function column_default( $item, $column_name ) {
		return $item[ $column_name ];
	}






	function connect_fs($url, $method, $context, $fields = null)
{
  global $wp_filesystem;
  if(false === ($credentials = request_filesystem_credentials($url, $method, false, $context, $fields))) 
  {
    return false;
  }

  //check if credentials are correct or not.
  if(!WP_Filesystem($credentials)) 
  {
    request_filesystem_credentials($url, $method, true, $context);
    return false;
  }

  return true;
}

function write_file_demo($text)
{
  global $wp_filesystem;

  $url = wp_nonce_url("options-general.php?page=demo", "filesystem-nonce");
  $form_fields = array("file-data");

  if(connect_fs($url, "", WP_PLUGIN_DIR . "/filesystem/filesystem-demo", $form_fields))
  {
    $dir = $wp_filesystem->find_folder(WP_PLUGIN_DIR . "/filesystem/filesystem-demo");
    $file = trailingslashit($dir) . "demo.txt";
    $wp_filesystem->put_contents($file, $text, FS_CHMOD_FILE);

    return $text;
  }
  else
  {
    return new WP_Error("filesystem_error", "Cannot initialize filesystem");
  }
}
}