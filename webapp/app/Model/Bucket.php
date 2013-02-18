<?php
App::uses('AppModel', 'Model');
class Bucket extends AppModel {
  
  public $order = array('Bucket.name' => 'ASC');
  
  public $belongsTo = array(
      'Account' => array(
          'className' => 'Account',
          'foreignKey' => 'account_id',
          'conditions' => '',
          'fields' => '',
          'order' => ''
      ),
  );
  
  public $hasMany = array(
  		'TransactionEntry' => array(
  				'className' => 'TransactionEntry',
  				'foreignKey' => 'bucket_id',
  				'dependent' => true,
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
  
  public static function clearBucketCache($accountId,$bucketIds)
  {
    $keys = array();
    $keys[] = self::getBucketCacheKey($accountId);
    foreach ($bucketIds as $bucketId)
    {
      $keys[] = self::getTransactionEntriesCacheKey($bucketId);
    }
    foreach ($keys as $key)
    {
      Cache::delete($key);
    }
  }

  public static function getBucketCacheKey($accountIds)
  {
    if (!is_array($accountIds)) $accountIds = array($accountIds);
    return 'buckets_for_accounts_' . implode('_',$accountIds);
  }
  
  public function getBucketsForAccounts($accountIds,$fields=array(),$contains=array())
  {
    $key = self::getBucketCacheKey($accountIds);
    if (($buckets = Cache::read($key)) !== false) return $buckets;
    $conditions = array($this->alias . '.account_id' => $accountIds);
    $buckets = $this->find('all',compact('fields','conditions','contain'));
    if (false !== $buckets) Cache::write($key, $buckets);
    return $buckets;
  }
  
  public static function getTransactionEntriesCacheKey($bucketId)
  {
    return "bucket_transaction_entries_{$bucketId}";
  }
  
  public function getTransactionEntries($bucketId)
  {
    $key = self::getTransactionEntriesCacheKey($bucketId);
    if (false !== ($bucket = Cache::read($key))) return $bucket;
    $conditions = array('Bucket.id' => $bucketId);
    $fields = array('Bucket.id','Bucket.name');
    $contain = array(
        'TransactionEntry' => array(
            'fields' => array('TransactionEntry.id','TransactionEntry.date','TransactionEntry.label','TransactionEntry.notes','TransactionEntry.bucket_before','TransactionEntry.amount','TransactionEntry.bucket_after'),
            'Transaction' => array('fields' => array('Transaction.id','Transaction.transaction_type_id','Transaction.amount'))
        )
    );
    $bucket = $this->find('first',compact('fields','conditions','contain'));
    if (false !== $bucket) Cache::write($key, $bucket);
    return $bucket;
  }
  
}
