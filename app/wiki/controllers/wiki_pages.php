<?php


function get( &$vars ) {
  extract( $vars );
  switch ( count( $collection->members )) {
    case ( 1 ) :
      if ($request->id && $request->entry_url())
        render( 'action', 'entry' );
    default :
      render( 'action', 'index' );
  }
}


function post( &$vars ) {
  extract( $vars );
  $resource->insert_from_post( $request );
  header_status( '201 Created' );
  redirect_to( array('resource'=>'wikis','id'=>$request->params['wikipage']['parent_id']) );
}


function put( &$vars ) {
  extract( $vars );
  $resource->update_from_post( $request );
  header_status( '200 OK' );
  redirect_to( array('resource'=>'wikis','id'=>$request->params['id']) );
}


function delete( &$vars ) {
  extract( $vars );
  $resource->delete_from_post( $request );
  header_status( '200 OK' );
  redirect_to( 'wikis' );
}


function _doctype( &$vars ) {
  // doctype controller
}



function index( &$vars ) {
  extract( $vars );
  $theme = environment('theme');
  $blocks = environment('blocks');
  $atomfeed = $request->feed_url();
  return vars(
    array(
      &$blocks,
      &$profile,
      &$collection,
      &$atomfeed,
      &$theme
    ),
    get_defined_vars()
  );
}


function _index( &$vars ) {
  // index controller returns
  // a Collection of recent entries
  extract( $vars );
  return vars(
    array( &$collection, &$profile ),
    get_defined_vars()
  );
}


function _entry( &$vars ) {
  // entry controller returns
  // a Collection w/ 1 member entry
  extract( $vars );
  $Member = $collection->MoveNext();
  $Entry = $Member->FirstChild( 'entries' );
  return vars(
    array( &$collection, &$Member, &$Entry, &$profile ),
    get_defined_vars()
  );
}


function _new( &$vars ) {
  extract( $vars );
  $model =& $db->get_table( $request->resource );
  $Member = $model->base();
  return vars(
    array( &$Member, &$profile ),
    get_defined_vars()
  );
}


function _edit( &$vars ) {
  extract( $vars );
  $Member = $collection->MoveFirst();
  $Entry = $Member->FirstChild( 'entries' );
  return vars(
    array( &$Member, &$Entry, &$profile ),
    get_defined_vars()
  );
}


function _remove( &$vars ) {
  extract( $vars );
  $Member = $collection->MoveFirst();
  $Entry = $Member->FirstChild( 'entries' );
  return vars(
    array( &$Member, &$Entry, &$profile ),
    get_defined_vars()
  );
}


function _block( &$vars ) {

  extract( $vars );
  return vars(
    array(
      &$Entry,
      &$collection
    ),
    get_defined_vars()
  );

}

