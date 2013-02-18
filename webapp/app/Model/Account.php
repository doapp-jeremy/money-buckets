<?php
App::uses('AppModel', 'Model');
class Account extends AppModel {
  
  public $belongsTo = array(
      'Creator' => array(
          'className' => 'User',
          'foreignKey' => 'user_id',
          'conditions' => '',
          'fields' => '',
          'order' => ''
      ),
  );

  public $hasMany = array(
      'Bucket' => array(
          'className' => 'Bucket',
          'foreignKey' => 'account_id',
          'dependent' => false,
          'conditions' => '',
          'fields' => '',
          'order' => '',
          'limit' => '',
          'offset' => '',
          'exclusive' => '',
          'finderQuery' => '',
          'counterQuery' => ''
      )
  );
  
  
  public $hasAndBelongsToMany = array(
//       'Friend' => array(
//           'className' => 'User',
//           'joinTable' => 'accounts_users',
//           'foreignKey' => 'account_id',
//           'associationForeignKey' => 'facebook_id',
//           'unique' => 'keepExisting',
//           'conditions' => '',
//           'fields' => '',
//           'order' => '',
//           'limit' => '',
//           'offset' => '',
//           'finderQuery' => '',
//           'deleteQuery' => '',
//           'insertQuery' => ''
//       ),
      'User' => array(
          'className' => 'User',
          'joinTable' => 'accounts_users',
          'foreignKey' => 'account_id',
          'associationForeignKey' => 'user_id',
          'unique' => false,
          'conditions' => '',
          'fields' => '',
          'order' => '',
          'limit' => '',
          'offset' => '',
          'finderQuery' => '',
          'deleteQuery' => '',
          'insertQuery' => ''
      )
  );
   
//   public function afterSave($created)
//   {
//     if ($created)
//     {
//       $data = array(
//         array(
//             'Account' => array('id' => $this->data['Account']['id']),
//             'Bucket' => array(
//                 'name' => 'Unallocated',
//                 'description' => 'Unallocated Funds'
//             )
//         )  
//       );
//       $Bucket = ClassRegistry::init('Bucket');
//       // ignore save failure
//       if (!$Bucket->saveAll($data))
//       {
//         debug($data);
//         exit();
//       }
//     }
//     return parent::afterSave($created);
//   }
  
}
