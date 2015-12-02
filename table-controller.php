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

	function column_stringresult( $item ) {
		$actions = array(
			'edit' => sprintf(
				'<a href="%s">%s</a>',
				esc_url( $item['editurl'] ),
				__( 'Edit' )
			)
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
}