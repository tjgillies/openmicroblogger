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
  
  $nickname = '';
  $letters = str_split(strtolower($request->params['blog']['title']));
  
  foreach ($letters as $letter)
    if (ereg("([a-z])", $letter))
      $nickname .= $letter;
  
  $prefix = substr($nickname,0,2);
  
  for ($i=0;$i<10;$i++) {
    $b = $Blog->find_by('prefix',$prefix);
    if (!$b && !in_array($prefix."_db_sessions",$db->tables) && strlen($prefix) > 1)
      continue;
    else
      $prefix = randomstring(2);
  }

  $request->set_param( array( 'blog', 'prefix' ), $prefix );
  $request->set_param( array( 'blog', 'nickname' ), $nickname );
  
  $resource->insert_from_post( $request );
  
  header_status( '201 Created' );
  redirect_to( $request->url_for('admin').'#ui-tabs-11' );
  
}


function put( &$vars ) {
  extract( $vars );
  $resource->update_from_post( $request );
  header_status( '200 OK' );
  redirect_to( $request->resource );
}


function delete( &$vars ) {
  extract( $vars );
  if (!member_of('administrators'))
    trigger_error('you must be an administrator to do that', E_USER_ERROR);
  $b = $collection->MoveFirst();
  if (!empty($b->prefix)) {
    $Shortener =& $db->model('Shortener');
    $short = $Shortener->find_by('nickname',$b->nickname);
    if ($short)
      $db->delete_record($short);
    $tabresult = $db->get_result("SHOW tables");
    $tables = array();
    for($i=0;$tables[$i]=mysql_fetch_assoc($tabresult);$i++) {
      $key = $tables[$i]['Tables_in_'.$db->dbname];
      if (substr($key,0,3) == $b->prefix.'_' && !in_array($key,array('categories_entries','aggregates_entries','twitter_users','db_sessions'))) {
        $sql = "DROP TABLE ".$key;
        $result = $db->get_result( $sql );
      }
    }
  }
  $resource->delete_from_post( $request );
  header_status( '200 OK' );
  redirect_to( $request->resource );
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

function _mystreams( &$vars ) {
  // index controller returns
  // a Collection of recent entries
  extract( $vars );
  
  $Blog =& $db->model('Blog');
  $Blog->set_param( 'find_by', array(
    'entries.person_id'=>get_person_id()
  ));
  $collection = new Collection('blogs');
    
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

function _newajax( &$vars ) {
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

