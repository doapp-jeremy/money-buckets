<?php
App::uses('AppModel', 'Model');
class Bucket extends AppModel {
  
  public $belongsTo = array(
      'Account' => array(
          'className' => 'Account',
          'foreignKey' => 'account_id',
          'conditions' => '',
          'fields' => '',
          'order' => ''
      ),
  );
  
//   public $hasAndBelongsToMany = array(
//       'Account' => array(
//           'className' => 'Account',
//           'joinTable' => 'accounts_buckets',
//           'foreignKey' => 'bucket_id',
//           'associationForeignKey' => 'account_id',
//           'unique' => 'keepExisting',
//           'conditions' => '',
//           'fields' => '',
//           'order' => '',
//           'limit' => '',
//           'offset' => '',
//           'finderQuery' => '',
//           'deleteQuery' => '',
//           'insertQuery' => ''
//       )
//   );
  
  
}
