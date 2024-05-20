<?php
/**
 * 
 * @desc <RaiseGen>Auto Generated model
 */

namespace src\models;

use framework\inc\Raise;
use framework\lib\Database;
use framework\lib\Router;
use src\traits\DataTableTrait;
use src\traits\FilterTrait;
use src\traits\ModelTrait;
use framework\lib\Helper as H;

/**
 * @property int(10) unsigned $id
 * @property varchar(50) $tag_name
 * @property varchar(5) $currency
 * @property tinyint(1) $status
 * @property int(11) $created_at
 * @property int(10) unsigned $created_by
 * @property varchar(15) $created_ip
 * @property int(11) $updated_at
 * @property int(10) unsigned $updated_by
 * @property varchar(15) $updated_ip
 **/

class PromoList extends Database
{
    use ModelTrait, FilterTrait, DataTableTrait;
    protected $pk = 'id';
    /**
     * Constructor of the model
     */
    public function __construct($db = "db")
    {
        parent::__construct(Raise::db()[$db]);
        $this->tableName = "promo_list";
        //$this->assignAttrs();

        $this->columns = [
            'id', //1
            'promo_type', //2
            'promo_name',
            'promo_start_time', //5
            'promo_end_time', //6
            'player_enroll',
            'player_complete',
            'player_count',
            'created_by',
            'display_status',
            'status',
            'manual_start_status', //6
            'target_type', //6-----
            'target_list', //6
            'target_list_doc', //6
            'member_level_list', //6
            'member_level_list_names', //6
            'device', //6
            'promo_sub_type', //3
            'verify_details', //6
            'conflict_promos', //6
            'apply_type', //6
            'stack',
            'is_hot_promo',
            'unique_ip_apply_count', //6
            'unique_name_apply_count', //6
            'unique_appid_apply_count', //6
            'max_apply_count', //6
            'player_daily_apply_count', //6
            'max_budget', 
            'finish_turnover_times', 
            'min_deposit_amount', 
            'min_deposit_count', 
            'min_bet_amount', 
            'min_bet_count',
            'payout_amount_cal_type', //6
            'payout_period_type', //6
            'payout_condition_type', //6
            'payout_timing_type', //6
            'payout_timing_hours', //6
            'payout_timing_mins', //6
            'payout_timing_start_time', //6
            'payout_timing_end_time', //6
            'payout_collection_type', //6
            'payout_collection_days_limit', //6
            'payout_verify_type', //6
            'custom_promo_payout_type',
            'min_daily_deposit', //6
            'min_daily_turnover', //6
            'vip_setting_type', //6
            'game_types', //6
            'game_vendors', //6
            'game_vendors_games', //6
            'game_vendors_wallet_id',
            'display_time_type', //6
            'display_start_time', //6
            'display_end_time',
            'display_type', //6
            'display_type_url', //6
            'display_type_new_tab', //6
            'apply_msg_title', //6
            'apply_msg_content', //6
            'success_msg_title', //6
            'success_msg_content', //6
            'status', //6
            'delete_status', //6
            'created_at', //6
            'created_by', //6
            'created_ip', //6
            'updated_at', //6
            'updated_by', //6
            'updated_ip', //6
            'deleted_at', //6
            'deleted_by', //6
            'deleted_ip', //6
        ];
    }

    /**
     *
     * @return Array
     */
    public static function attrs()
    {
        return ['id', 'promo_type', 'promo_sub_type', 'promo_name', 'promo_start_time', 'promo_end_time', 'target_type', 'target_list', 'target_list_doc', 'member_level_list', 'member_level_list_names', 'device', 'verify_details', 'conflict_promos', 'apply_type', 'stack', 'is_hot_promo', 'unique_ip_apply_count', 'unique_name_apply_count', 'unique_appid_apply_count', 'max_apply_count', 'player_daily_apply_count', 'max_budget', 'finish_turnover_times', 'min_deposit_amount', 'min_deposit_count', 'min_bet_amount', 'min_bet_count', 'payout_amount_cal_type', 'payout_period_type', 'payout_condition_type', 'payout_timing_type', 'payout_timing_hours', 'payout_timing_mins', 'payout_timing_seconds', 'payout_timing_start_time', 'payout_timing_end_time', 'payout_collection_type', 'payout_collection_days_limit', 'payout_verify_type', 'custom_promo_payout_type', 'min_daily_deposit', 'min_daily_turnover', 'sign_days', 'sign_days_bonus', 'sign_days_turnover_times', 'sign_recover_days_limit', 'sign_recover_deposit_amount', 'sign_recover_deposit_count', 'sign_recover_turnover', 'sign_recover_bet_count', 'sign_recover_bet_amount', 'vip_setting_type', 'game_selection_type', 'game_types', 'game_vendors', 'game_vendors_games', 'game_vendors_wallet_id', 'display_time_type', 'display_start_time', 'display_end_time', 'display_type', 'display_type_url', 'display_type_new_tab', 'apply_msg_title', 'apply_msg_content', 'success_msg_title', 'success_msg_content', 'apply_msg_id', 'success_msg_id', 'status', 'delete_status', 'manual_start_status', 'created_at', 'created_by', 'created_ip', 'updated_at', 'updated_by', 'updated_ip', 'deleted_at', 'deleted_by', 'deleted_ip'];
    }

    /**
     *
     * @return $this
     */
    public function assignAttrs($attrs = [])
    {   
        $isExternal = !empty($attrs);
        foreach (($isExternal ? $attrs : self::attrs()) as $eAttr => $attr) {
            $aAttr = $isExternal ? $eAttr : $attr;
           $this->{$aAttr} = $isExternal ? $attr : "";
        }
        
        return $this;
    }


    /**
     *
     * @param INT $pk
     */
    public function findByPK($pk)
    {
        $dtAry = parent::findByPK($pk);
        foreach ($dtAry as $attr => $val) {
            $this->{$attr} = $val;
        }
        return $this;
    }

   
    /**
     *
     * @return attrs data array
     */
    public function convertArray()
    {
        $temp = array();
        $attrs = $this->attrs();
        foreach ($attrs as $key) {
            $temp[$key] = isset($this->{$key})?$this->{$key}:'';
        }
        return $temp;
    }

    /**
     *
     * @return description of promo
     */
    public function getPromoDescription($data){
        $id = isset($data['id'])?$data['id']:'';
        if(!empty($id)){
            $explodeData    = explode('@',$id);
            $promoId        = $explodeData['0'];     
            $language       = isset($explodeData[1])?$explodeData[1]:'en';     
            $descType       = $explodeData[2]; 
            if($descType == 'promo_web'){
                $selectVarible = 'pl.description_web';
            }
            else if($descType == 'promo_h5'){
                $selectVarible = 'pl.description_h5';
            }
            else{
                $selectVarible = 'pl.description_app';
            }

            $query = 'SELECT '.$selectVarible.' FROM '.$this->tableName.' AS p JOIN promo_list_display_language AS pl ON p.id = pl.promo_list_id WHERE p.delete_status = 0 AND pl.language_code = \''.$language.'\' AND p.id = \''.$promoId.'\' ';

            $this->query($query);
            $data = $this->getValue();
            $data = !empty($data)?html_entity_decode($data):'';
            return array('success'=>true,'data'=>array("content"=>$data));


        }

    }
   
    /**
     * Get record by "game type"
     * 
     * @param Int $vendorGameCategoryId
     * @return Array
     */

    public function getPromoSetting($data)
    {  
        $language     = !empty($data['language'])?$data['language']:'en';
        $time = time();
        $category     = $data['category'];
        $promoId      = $data['promoId'];
        $whr          = [];
        $dataArray    = [];
        $orderByQuery = $whereAllDetail = '';
        $query  = 'SELECT ';
        $query .= '     promo_list.id,
                        promo_list.promo_name,
                        promo_list.promo_type,
                        promo_list.is_hot_promo,
                        promo_list.promo_start_time,
                        promo_list.promo_end_time,
                        promo_list.display_time_type,
                        promo_list.display_start_time,
                        promo_list.display_end_time,
                        promo_list.manual_start_status,
                        promo_list_display_language.banner_1,
                        promo_list_display_language.banner_2,
                        promo_list_display_language.icon,
                        promo_list_display_language.title
                ';
        $query .= ' FROM promo_list JOIN promo_list_display_language ON promo_list.id = promo_list_display_language.promo_list_id   ';
        array_push($whr,'  promo_list.delete_status = 0 ');
        // array_push($whr,'  promo_list.status = 1 ');
        array_push($whr,'  promo_list_display_language.language_code = "'.$language.'" ');
        array_push($whr, ' ( CASE 
                WHEN promo_list.display_time_type = 1 THEN 
                ( CASE 
                    WHEN promo_list.promo_end_time != 0 THEN   promo_list.promo_start_time <= '.$time.' AND (promo_list.promo_end_time) >= '.$time.'  
                    WHEN promo_list.promo_end_time = 0 THEN  promo_list.promo_start_time <= '.$time.' 
                    ELSE 1
                END )
                WHEN promo_list.display_time_type = 3 THEN  
                    ( CASE 
                        WHEN promo_list.display_end_time != 0 THEN   promo_list.display_start_time <= '.$time.' AND (promo_list.display_end_time) >= '.$time.' 
                        WHEN promo_list.display_end_time = 0 THEN  promo_list.display_start_time <= '.$time.' 
                        ELSE 1
                    END )
                ELSE 1
            END ) ');

        if($category != 'ALL' && $category != ''){
            array_push($whr,'  FIND_IN_SET("'.$category.'",promo_list.game_types) ');
            $whereAllDetail .= ' WHERE FIND_IN_SET("'.$category.'",promo_list.game_types) ';
        }
         if($promoId != ''){
            array_push($whr,'  promo_list.id = "'.$promoId.'" ');

        }
        else{
            $orderByQuery .= ' ORDER BY promo_list.sort_order ASC ';
        }

        if (!empty($data['promoName'])) {
            array_push($whr,' promo_list_display_language.title LIKE \'%'.$data['promoName'].'%\' ');

            $whereAllDetail = ' AND promo_list_display_language.title LIKE \'%'.$data['promoName'].'%\' ';
        }

        $limitStr = '';
        if (isset($data['start']) && isset($data['length'])) { 
            $limitStr = ' LIMIT '.$data['start'].', '.$data['length'].' ';
        } else {
            $limitStr = ' LIMIT 1 ';
        }

        $whereDt = ' ';
        if (count($whr)) {
            $whereDt = $whereDt === '' ?
            implode(' AND ', $whr) :
            $whereDt . ' AND ' . implode(' AND ', $whr);
        }
        if (!empty($whereDt)) {
            $whereDt = 'WHERE 1 ' . $whereDt;
        }
        if(empty($promoId)){
            $queryAll = $query.$whereAllDetail.' ORDER BY promo_list.sort_order ASC '.$limitStr;
            $this->query($queryAll);
            $dataArray['promoSettingList'] = $this->resultset();
            
        }

        $queryDisp = $query.$whereDt.$orderByQuery;  
        $this->query($queryDisp);
        $dataList = $this->resultset();
        foreach($dataList as $index=>$value) {
                if(!empty($value['promo_start_time'])) {
                    $dataList[$index]['promo_start_time'] = date('Y-m-d',$dataList[$index]['promo_start_time']);
                }
                else{
                   $dataList[$index]['promo_start_time'] = ''; 
                }  
                if(!empty($value['promo_end_time'])){
                    $dataList[$index]['promo_end_time'] = date('Y-m-d',$dataList[$index]['promo_end_time']);
                } 
                else{
                   $dataList[$index]['promo_end_time'] = ''; 
                }

                if(!empty($value['display_start_time'])) {
                    $dataList[$index]['display_start_time'] = date('Y-m-d',$dataList[$index]['display_start_time']);
                }
                else{
                   $dataList[$index]['display_start_time'] = ''; 
                }  
                if(!empty($value['display_end_time'])){
                    $dataList[$index]['display_end_time'] = date('Y-m-d',$dataList[$index]['display_end_time']);
                } 
                else{
                   $dataList[$index]['display_end_time'] = ''; 
                }   
            }

        $dataArray['promoDefault'] = $dataList;

        return $dataArray;

    }

     /**
     * getRecords
     *
     * @param array $prms
     * @return mixed $collection
     */

    public function getRecords($prms = [], $select = null, $isDatatable = true)
    {
        $data = [];
        $draw = $prms['draw'] ?? 0;
        $prms['length'] = $prms['length'] ?? 10;
        $prms['start'] = $prms['start'] ?? 0;
        $prms['order'] = $prms['order'] ?? array(array('column' => 1, 'dir' => 'asc'));

        $limitDt = $this->limit($prms, $this->columns);
        $orderDt = $this->order($prms, $this->columns);
        $nowtime = time();
        $lang = Raise::$lang; 

        $whr = [];
        $select = $select ?? [
            'promo_list.id',
            'promo_list.promo_type as promo_type',
            'promo_list.promo_name as promo_name',
            'promo_list.promo_start_time as promo_start_time',
            'promo_list.promo_end_time as promo_end_time',
            'promo_list.created_by as created_by',
            'promo_list.manual_start_status as manual_start_status',
            'promo_list.status as status',
            'promo_list.display_time_type as display_time_type',
            'promo_list.angpao_is_enabled as angpao_is_enabled',
            'promo_list.payout_timing_type as payout_timing_type',
            'promo_list.game_types as game_types',
            'CASE 
             WHEN '.$nowtime.' < promo_start_time THEN "0"
             WHEN '.$nowtime.' > promo_end_time  AND promo_end_time != 0  THEN "2"
             ELSE (CASE WHEN status = 0 THEN "3" ELSE "1" END)
            END AS display_status'
        ];
        $join = [];
        
        /* --------------------------- Filter --------------------------- */
        if (issetNotEmpty($prms, 'promo_title')) {
            $promo_title = $prms['promo_title'];
        // $join = ['LEFT JOIN promo_list_display_language ON promo_list.id = promo_list_display_language.promo_list_id'];
            array_push($whr, " promo_list.id IN (SELECT promo_list_display_language.promo_list_id FROM promo_list_display_language WHERE promo_list_display_language.title LIKE '%".$promo_title."%' AND promo_list_display_language.language_code = '".$lang."')  ");
            // array_push($whr, "promo_list_display_language.language_code = '".$lang."' ");

        }
        if (issetNotEmpty($prms, 'promo_id')) {
            $promo_id = $prms['promo_id'];
            array_push($whr, "promo_list.id IN ($promo_id)");
        }

        if (isset($prms['promo_status']) && in_array($prms['promo_status'], ['0', '1'])) {
            $status = $prms['promo_status'];
            array_push($whr, "promo_list.status = '$status'");
        }

        if (issetNotEmpty($prms, 'promo_category')) {
            $promo_category = $prms['promo_category'];
            array_push($whr, " FIND_IN_SET ('".$promo_category."',promo_list.game_types)");
        }
         if (issetNotEmpty($prms, 'promo_type')) {
            $promo_type = $prms['promo_type'];
            array_push($whr, "promo_list.promo_type IN ($promo_type)");
        }

        if (issetNotEmpty($prms, 'join_date')) {
            $join_date = $prms['join_date'];
            $expldeDate = explode(' - ', $join_date);
             if (!empty($expldeDate)){
                $startDate = !empty($expldeDate[0])?strtotime($expldeDate[0].' 00:00:00'):'';  
                $endDate = !empty($expldeDate[1])?strtotime($expldeDate[1].' 23:59:59'):'';  
                if(!empty($startDate) && !empty($endDate)){
                    array_push($whr, " $startDate <= promo_list.promo_start_time AND $endDate >= promo_list.promo_end_time ");
                }
             }
        }

        /* --------------------------- Filter --------------------------- */

        $whereDt = ' delete_status = 0 ';
        if (count($whr)) {
            $whereDt = $whereDt === '' ?
            implode(' AND ', $whr) :
            $whereDt . ' AND ' . implode(' AND ', $whr);
        }
        if ($whereDt !== '') {
            $whereDt = 'WHERE ' . $whereDt;
        }

        if ($isDatatable) {
            $data['draw'] = (int) $draw;
            $rtq = "SELECT COUNT(id) FROM " . $this->tableName . " " . implode(' ', $join). " $whereDt";
            $this->query($rtq);
            $data['recordsTotal'] = (int) $this->getValue();
        }

        $flq = "SELECT COUNT(id) FROM " . $this->tableName . " " . implode(' ', $join) . " $whereDt";
        $this->query($flq);
        $data['recordsFiltered'] = (int) $this->getValue();
        $data['startPage'] = $prms['start'];
        $createdByArr = [];
        $microArr = [];
        $grandtotal  = [];
        $promo_count = [];
        $promo_joined_sub = 0;

        /* --------------------------- Grand Total display --------------------------- */

        if($prms['start'] == 0) {

            $this->query("SELECT * FROM (SELECT count(DISTINCT player_id) as promo_joined FROM promo_activity_list WHERE promo_activity_list.promo_list_id IN 
                ( SELECT promo_list.id FROM promo_list $whereDt)) as  grandTotal1,
                    ( SELECT count(player_id) as promo_applied FROM promo_activity_list WHERE  promo_activity_list.promo_list_id IN 
                            ( SELECT promo_list.id FROM promo_list $whereDt)) as grandTotal2,
                                (SELECT count(player_id) as promo_complete  FROM promo_activity_list WHERE promo_status IN (2,3) AND  promo_activity_list.promo_list_id IN 
                                    ( SELECT promo_list.id FROM promo_list $whereDt)) as grandTotal3 ");
            $promo_count = $this->resultset();
            $grandtotal = array("promo_joined"=>$promo_count[0]['promo_joined'],"promo_complete"=>$promo_count[0]['promo_complete'],"promo_applied"=>$promo_count[0]['promo_applied']);
        }

        /*$this->query("SELECT count(DISTINCT player_id) as joined_sub FROM promo_activity_list LEFT JOIN promo_list ON promo_list.id = promo_activity_list.promo_list_id  $whereDt ORDER BY promo_activity_list.id $limitDt");

        $promo_joined_sub = $this->getValue();*/
        $this->query("SELECT count(DISTINCT player_id) as joined_sub FROM promo_activity_list LEFT JOIN promo_list ON promo_list.id = promo_activity_list.promo_list_id  $whereDt GROUP BY (promo_activity_list.promo_list_id)");
        $promo_joined_sub=0;
        foreach($this->resultset() as $k=>$v) {
            $promo_joined_sub+=$v['joined_sub'];
         }
        $this->query("SELECT game_type,game_type_name    FROM game_vendors_list GROUP BY game_type");
        $array_game_category=[];
        foreach($this->resultset() as $k=>$v) {
            $array_game_category[$v['game_type']] =$v['game_type_name'];
         }
        $promo_joined_sub = !empty($promo_joined_sub)?$promo_joined_sub:'0';

        $data['grandTotal'] = array_merge($grandtotal,array("promo_joined_sub"=>$promo_joined_sub));
        $data['subTotalPlayerComplete'] = 0;
        $data['subTotalPlayerEnroll'] = 0;
        $data['subTotalPlayerCount'] = 0;

        /* --------------------------- Grand Total display --------------------------- */

        $q = "SELECT " . implode(',', $select) . ", (SELECT COUNT(DISTINCT player_id) FROM `promo_activity_list` WHERE find_in_set(promo_list.id, promo_activity_list.promo_list_id)) as player_enroll , (SELECT COUNT(*) FROM `promo_activity_list` WHERE find_in_set(promo_list.id, promo_activity_list.promo_list_id)) as player_count, (SELECT COUNT(*) FROM `promo_activity_list` WHERE find_in_set(promo_list.id, promo_activity_list.promo_list_id) and promo_status IN (2,3)) as player_complete FROM promo_list " . implode(' ', $join) . " $whereDt $orderDt $limitDt";
        if ($data['recordsFiltered'] > 0) {
            $this->query($q);
            
            $data['data'] = $this->resultset();
            $subTotalPlayerComplete= 0;
            $subTotalPlayerEnroll= 0;
            $subTotalPlayerCount= 0;

            $promo_ids_array = array_column($data['data'], 'id');

            $run_count_array = array();
            if (!empty($promo_ids_array)) {
                $run_sql = 'SELECT promo_list_id, COUNT(*) AS run_count FROM promo_activity_list WHERE promo_list_id IN ('.implode(',', $promo_ids_array).') AND promo_status=1 GROUP BY promo_list_id';
                $this->query($run_sql);

                $running_result = $this->resultset();
                $run_count_array = !empty($running_result)?array_column($running_result, 'run_count', 'promo_list_id'):array();
            }

            foreach ($data['data'] as $index => $value) {
                $subTotalPlayerComplete +=$value['player_complete'];
                $subTotalPlayerEnroll +=$value['player_enroll'];
                $subTotalPlayerCount +=$value['player_count'];

              //  return $value;
                foreach ($value as $key => $kValue) {
                    if ($key == 'created_by') {
                        $microArr [] = [
                            'serviceName' => MICRO_SERVICES['admin'],
                            'command' => 'subadmin/getsubadmindetails',
                            'param' => ['adminuserid'=>$kValue],
                        ];
                    }
                }
                if($value['promo_type'] == '6'){
                    $this->query("SELECT count(*) FROM promo_activity_list WHERE promo_list_id = $value[id] and promo_type = 6 and custom_promo_answers != '' ");
                    $custom_status = $this->getValue();
                    $data['data'][$index]['custom_count'] = $custom_status ?? '';
                }
                if(!empty($value['promo_name'])){
                    $this->query("SELECT title FROM promo_list_display_language WHERE promo_list_id = $value[id] and language_code = '$lang' ");
                    $promoname_lang = $this->getValue();
                    $data['data'][$index]['promo_name'] = $promoname_lang ?? '';
                }
                else{
                    $data['data'][$index]['promo_name'] = '-'; 
                }

                $data['data'][$index]['running_count'] = !empty($run_count_array[$value['id']])?$run_count_array[$value['id']]:0;

                /*if($data['data'][$index]['display_status'] == 1){
                    if($data['data'][$index]['status'] == 0){
                        $data['data'][$index]['display_status'] = 3;//Disabled
                    }
                }*/

                if($data['data'][$index]['game_types'] != ''){
                    $game_types_id = $data['data'][$index]['game_types'];
                    $explode_game = explode(',', $game_types_id);
                    $game_type_array = [];
                    foreach ($explode_game as $key => $value) {
                        if(isset($array_game_category[$value])){
                            array_push($game_type_array,$array_game_category[$value]);
                        }
                    }
                    $data['data'][$index]['game_types'] = $game_type_array;

                   
                }

                if(!empty($data['data'][$index]['promo_start_time'])) {
                    $data['data'][$index]['promo_start_time'] = date('Y-m-d',$data['data'][$index]['promo_start_time']);
                }
                else{
                   $data['data'][$index]['promo_start_time'] = ''; 
                }  
                if(!empty($data['data'][$index]['promo_end_time'])){
                    $data['data'][$index]['promo_end_time'] = date('Y-m-d',$data['data'][$index]['promo_end_time']);
                } 
                else{
                   $data['data'][$index]['promo_end_time'] = '-'; 
                }   

            }

            $data['subTotalPlayerComplete'] = $subTotalPlayerComplete;
            $data['subTotalPlayerEnroll'] = $subTotalPlayerEnroll;
            $data['subTotalPlayerCount'] = $subTotalPlayerCount;
            
            if (!empty($microArr)) {
                $result = Raise::callApi($microArr);
                foreach ($result as $index => $adminArr) {
                    $data['data'][$index]['created_by'] = $adminArr['data']['admin_user_name'] ?? '';
                }
            }

        } else {
            $data['data'] = [];
        }

        return $data ;
    }

    public function checkIsAllowDelete($promo_id)
    {
        $sql = 'SELECT id, promo_end_time FROM '.$this->tableName.' WHERE id=\''.$promo_id.'\' AND delete_status=0';

        $promo_info = $this->callSql($sql, 'row');

        if (empty($promo_info)) {
            return false;
        }

        $running_count = $this->callSql('SELECT COUNT(*) FROM promo_activity_list WHERE promo_list_id=\''.$promo_id.'\' AND promo_status=1', 'value');

        if ($running_count <= 0 || (!empty($promo_info['promo_end_time']) && $promo_info['promo_end_time'] < time())) {
            return true;
        }

        return false;
    }

    public function getAllActivePromos($select=[]){

        $language = 'en';
        $time = time();

        $promos = $this->callSql("SELECT * FROM $this->tableName WHERE promo_start_time <= $time AND ((promo_end_time > 0 AND promo_end_time >= $time) OR promo_end_time <= 0) AND apply_type = 1 ","rows");

        $data = [];

        foreach($promos as $promo){

            $promo_name_json = json_decode($promo['promo_name'],true);

            $promo_name = $promo_name_json[$language] ?? "";

            $data[] = array(
                'promo_list_id' => $promo['id'],
                'promo_name' => $promo_name,
                'promo_link' => "promo/details/id/".$promo['id']
            );

        }

        return $data;

    }
    
    /**
     * update status
     *
     * @param array $data
     * @param int $id
     * @return boolean $status
     */
    public function updateStatusRecord($data, $id)
    {
        $rec = $this->findByPK($id);
        $status_update  = $data['status'];
        $updatedBy      = $data['updatedBy'];
        if (is_numeric($rec->id)) {
            if($status_update == 1){
                if($rec->promo_type == 8){
                    $start_time = $rec->promo_start_time;
                    $end_time = $rec->promo_end_time;
                $promoExist = $this->callSql("SELECT id,promo_start_time,promo_end_time FROM promo_list WHERE id != $rec->id AND status = 1 AND promo_sub_type = $rec->promo_sub_type AND promo_type = 8 AND (CASE 
                    WHEN ($end_time = 0 && promo_end_time = 0 ) THEN 1
                    WHEN ($start_time <= promo_end_time || promo_start_time <=  $end_time ) THEN 1 END )
                      ","rows");
                    if(!empty($promoExist)){
                        // foreach ($promoExist as $key => $value) {

                        // if( $start_time <= $promoExist['promo_end_time']  || ($end_time == 0 &&  $promoExist['promo_end_time'] == 0) || $promoExist['promo_start_time'] <= $end_time) {
                            
                        return ['status'=>false,'message'=>"Cannot Enable More than one Mission Promo at the Same time","error_code"=>"EP102"];
                            
                    //     }

                    // }
                    }


                }
            }
            $this->query("UPDATE promo_list SET status=:status, updated_by=:updated_by, updated_ip=:updated_ip, updated_at=:updated_at WHERE id =:id");
            $this->bind('status', $status_update);
            $this->bind('id', $id);
            $this->bind('updated_by', $updatedBy);
            $this->bind('updated_ip', getClientIP());
            $this->bind('updated_at', time());
            $this->execute();
            return ['status'=>true,'message'=>"Successfully Updated the Status","error_code"=>"SP003"];

        }
        return ['status'=>false,'message'=>"","error_code"=>"EP048"];
    }

    public function sortPromoList($data){
        $sortjson = $data['sortarray'];
        $sortArr = json_decode($sortjson,true);
        // return $sortArr;
        foreach ($sortArr as $key => $value) {
            $this->query("UPDATE promo_list SET sort_order=:sort_order WHERE id =:id ");
            $this->bind('sort_order', $value);
            $this->bind('id', $key);
            $this->execute();
        }
        return true;
    }

    /**
     * update manual status
     *
     * @param array $data
     * @param int $id
     * @return boolean $status
     */
    public function updateStatusManual($data, $id)
    {
        $rec = $this->findByPK($id);
        $status_update  = $data['manual_start_status'];

        if (is_numeric($rec->id)) {
            $this->query("UPDATE promo_list SET manual_start_status=:manual_start_status WHERE id =:id and display_time_type = :display_time_type");
            $this->bind('manual_start_status', $status_update);
            $this->bind('id', $id);
            $this->bind('display_time_type', 2);
            return $this->execute();

        }
    }

    /**
     * hot promo status
     *
     * @param array $data
     * @param int $id
     * @return boolean $status
     */
    public function updateHotPromoStatus($data, $id)
    {
        $rec = $this->findByPK($id);
        $status_update  = $data['is_hot_promo'];

        if (is_numeric($rec->id)) {
            $this->query("UPDATE promo_list SET is_hot_promo=:is_hot_promo WHERE id =:id ");
            $this->bind('is_hot_promo', $status_update);
            $this->bind('id', $id);
            return $this->execute();
        }

        return false;
    }

    /**
     * delete record
     *
     * @param int $id
     * @return boolean $status
     */
    public function deletePromoRecord($id,$data)
    {
        $rec = $this->findByPK($id);
        $status_update  = 1;
        $updatedBy      = $data['updatedBy'];

       if (is_numeric($rec->id)) {
            // if ($this->deleteByPK($id)) {
                 $this->query("UPDATE promo_list SET delete_status=:delete_status, deleted_by=:deleted_by, deleted_ip=:deleted_ip, deleted_at=:deleted_at WHERE id =:id");
                $this->bind('delete_status', $status_update);
                $this->bind('id', $id);
                $this->bind('deleted_by', $updatedBy);
                $this->bind('deleted_ip', getClientIP());
                $this->bind('deleted_at', time());
                return $this->execute();

            // }
        }
        return false;
    }

    /**
     * get promo list (name)
     *
     * @param int $id
     * @return boolean $status
     */
    public function getPromoSelect($promoType)
    {   

        $wherQuery = '';
        if(!empty($promoType)){
            $wherQuery = ' WHERE promo_type = '.$promoType;
        }
        $dataArray          = [];
        $query  = 'SELECT ';
        $query .= '     promo_list.id,
                        promo_list.promo_name
                ';
        $query .= ' FROM promo_list '.$wherQuery;
        // return $query;
        $this->query($query);
        $dataList = $this->resultset();
        $dataArray['promoList'] = $dataList;

        return $dataArray;
    }


      /**
     * get all promo list for display
     *
     * @param int $id
     * @return boolean $status
     */
    public function getAllPromoTotal($post)
    {   

        $language   = isset($post['language'])?$post['language']:Raise::$lang; 
        $promo_type = isset($post['promo_type'])?$post['promo_type']:'';
        $device     = isset($post['device'])?$post['device']:'1';
        $time       = time();

        $wherQuery  = ' AND  promo_list.status = 1 and delete_status=0 and promo_list.id = promo_list_display_language.promo_list_id and promo_list_display_language.language_code = "'.$language.'" ';

        if (!empty($device)) {
            $wherQuery .= ' AND (CASE WHEN promo_list.device != "" THEN FIND_IN_SET('.$device.',promo_list.device) ELSE 1 END ) ';
        }

        if (!empty($post['player_id'])) {

            $player_id = $post['player_id'];

            $user = ( isset($post['user_info']) && !empty($post['user_info']) ) ? $post['user_info'] : $this->getUserInfo($player_id);

            $affiliate_id         = $user['affiliate_id'] ?? 0;
            $player_group_id      = $user['player_group_id'] ?? 0;
            $mobile_verification_status = $user['mobile_verification_status'] ?? 0;
            $is__security_pin     = $user['is__security_pin'] ?? 0;
            $is__bank             = $user['is__bank'] ?? 0;
            $promo_block          = $user['promo_block'] ?? 0;

            if (!empty($promo_block)) {

                return 0;
                //$wherQuery .= ' AND 0 ';
            }

            $wherQuery .= ' AND apply_type = 1 AND 
                            ( CASE 
                                WHEN promo_list.target_type = 2  THEN FIND_IN_SET(\''.$affiliate_id.'\', promo_list.target_list)
                                WHEN promo_list.target_type = 3  THEN FIND_IN_SET(\''.$player_id.'\', promo_list.target_list) 
                                ELSE 1 END )';
            
            if(!empty($player_group_id)){
                $wherQuery .= ' AND 
                            ( CASE 
                                WHEN promo_list.member_level_list != ""   THEN FIND_IN_SET(\''.$player_group_id.'\', promo_list.member_level_list)
                                ELSE 1 END )
                                ';
            }

            // $wherQuery .= ' AND 
            //                 ( CASE 
            //                     WHEN (promo_list.verify_details != "" AND FIND_IN_SET(1, promo_list.verify_details) AND '.$mobile_verification_status.' != 1 )
            //                        THEN  0 ELSE 1  
            //                 END )';

            // $wherQuery .= ' AND 
            //                 ( CASE 
            //                     WHEN (promo_list.verify_details != "" AND FIND_IN_SET(2, promo_list.verify_details) AND   '.$is__security_pin.' = 0  )
            //                        THEN  0 ELSE 1  
            //                 END )';
            // $wherQuery .= ' AND 
            //                 ( CASE 
            //                     WHEN (promo_list.verify_details != "" AND FIND_IN_SET(3, promo_list.verify_details) AND '.$is__bank.' = 0)
            //                        THEN  0 ELSE 1  
            //                 END )';
                                        
            // $getAllAppliedPromos = $this->callSql("SELECT GROUP_CONCAT(DISTINCT promo_list_id SEPARATOR ',') FROM promo_activity_list WHERE player_id =".$player_id." ", "value");

            // $wherFindSet = '';

            // if(!empty($getAllAppliedPromos)){
            //     $explodeArr = explode(',',$getAllAppliedPromos);
            //     $find_setCond = '';
            //     foreach ($explodeArr as $key => $value) {
            //         if(!empty($find_setCond)){
            //             $find_setCond .= ' OR FIND_IN_SET('.$value.',promo_list.conflict_promos) ';

            //         }
            //         else{
            //             $find_setCond .= ' FIND_IN_SET('.$value.',promo_list.conflict_promos) ';

            //         }
            //     }
            //     if(count($explodeArr) > 1){
            //         $wherFindSet .= ' AND ('.$find_setCond.')';
            //     }
            //     else{
            //         $wherFindSet .= ' AND '.$find_setCond.'';
            //     }
            //     $wherQuery .= ' AND ( CASE 
            //                     WHEN (promo_list.conflict_promos !="" '.$wherFindSet.' ) THEN
            //                     0 ELSE 1 
            //                     END ) ';
            // }                     
                                 
        }
 
        if (!empty($promo_type)) { 
            if(is_array($promo_type)) {
              $promo_type = join(",",$promo_type);
            }
            $wherQuery .= ' AND promo_list.promo_type IN ('.$promo_type.') ';
        }
        
        if (!empty($promo_text)) { 
            $wherQuery .= ' AND promo_list.promo_name LIKE "%'.$promo_type.'%" ';
        }

        $sqlCount = 'SELECT COUNT(*) FROM promo_list,promo_list_display_language WHERE 
            ( CASE 
                WHEN promo_list.display_time_type = 1 THEN 
                ( CASE 
                    WHEN promo_list.promo_end_time != 0 THEN   promo_list.promo_start_time <= '.$time.' AND (promo_list.promo_end_time) >= '.$time.'  
                    WHEN promo_list.promo_end_time = 0 THEN  promo_list.promo_start_time <= '.$time.' 
                    ELSE 1
                END )
                WHEN promo_list.display_time_type = 2 THEN   promo_list.manual_start_status = 1  
                WHEN promo_list.display_time_type = 3 THEN  
                    ( CASE 
                        WHEN promo_list.display_end_time != 0 THEN   promo_list.display_start_time <= '.$time.' AND (promo_list.display_end_time) >= '.$time.' 
                        WHEN promo_list.display_end_time = 0 THEN  promo_list.display_start_time <= '.$time.' 
                        ELSE 1
                    END )
                ELSE 1
            END ) '.$wherQuery.' ';

        $this->query($sqlCount);

        $totalCount = $this->getValue();

        return $totalCount;
    }  



    public function getAllPromoTotal2($post)
    {   



        $language   = isset($post['language'])?$post['language']:Raise::$lang; 
        $promo_type = isset($post['promo_type'])?$post['promo_type']:'';
        $device     = isset($post['device'])?$post['device']:'1';
        $time       = time();

       



        $wherQuery  = ' AND  promo_list.status = 1 and delete_status=0 and promo_list.id = promo_list_display_language.promo_list_id and promo_list_display_language.language_code = "'.$language.'" ';

        if (!empty($device)) {
            $wherQuery .= ' AND FIND_IN_SET('.$device.',promo_list.device) ';
        }

        if (!empty($post['player_id'])) {

            $player_id = $post['player_id'];

            $user = ( isset($post['user_info']) && !empty($post['user_info']) ) ? $post['user_info'] : $this->getUserInfo($player_id);

            $affiliate_id         = $user['affiliate_id'] ?? 0;
            $player_group_id      = $user['player_group_id'] ?? 0;
            $mobile_verification_status = $user['mobile_verification_status'] ?? 0;
            $is__security_pin     = $user['is__security_pin'] ?? 0;
            $is__bank             = $user['is__bank'] ?? 0;
            $promo_block          = $user['promo_block'] ?? 0;

            if (!empty($promo_block)) {

                return 0;
                //$wherQuery .= ' AND 0 ';
            }

            $wherQuery .= ' AND apply_type = 1 AND 
                            ( CASE 
                                WHEN promo_list.target_type = 2  THEN FIND_IN_SET(\''.$affiliate_id.'\', promo_list.target_list)
                                WHEN promo_list.target_type = 3  THEN FIND_IN_SET(\''.$player_id.'\', promo_list.target_list) 
                                ELSE 1 END )';
            
            if(!empty($player_group_id)){
                $wherQuery .= ' AND 
                            ( CASE 
                                WHEN promo_list.member_level_list != ""   THEN FIND_IN_SET(\''.$player_group_id.'\', promo_list.member_level_list)
                                ELSE 1 END )
                                ';
            }

            // $wherQuery .= ' AND 
            //                 ( CASE 
            //                     WHEN (promo_list.verify_details != "" AND FIND_IN_SET(1, promo_list.verify_details) AND '.$mobile_verification_status.' != 1 )
            //                        THEN  0 ELSE 1  
            //                 END )';

            // $wherQuery .= ' AND 
            //                 ( CASE 
            //                     WHEN (promo_list.verify_details != "" AND FIND_IN_SET(2, promo_list.verify_details) AND   '.$is__security_pin.' = 0  )
            //                        THEN  0 ELSE 1  
            //                 END )';
            // $wherQuery .= ' AND 
            //                 ( CASE 
            //                     WHEN (promo_list.verify_details != "" AND FIND_IN_SET(3, promo_list.verify_details) AND '.$is__bank.' = 0)
            //                        THEN  0 ELSE 1  
            //                 END )';
                                        
            // $getAllAppliedPromos = $this->callSql("SELECT GROUP_CONCAT(DISTINCT promo_list_id SEPARATOR ',') FROM promo_activity_list WHERE player_id =".$player_id." ", "value");

            // $wherFindSet = '';

            // if(!empty($getAllAppliedPromos)){
            //     $explodeArr = explode(',',$getAllAppliedPromos);
            //     $find_setCond = '';
            //     foreach ($explodeArr as $key => $value) {
            //         if(!empty($find_setCond)){
            //             $find_setCond .= ' OR FIND_IN_SET('.$value.',promo_list.conflict_promos) ';

            //         }
            //         else{
            //             $find_setCond .= ' FIND_IN_SET('.$value.',promo_list.conflict_promos) ';

            //         }
            //     }
            //     if(count($explodeArr) > 1){
            //         $wherFindSet .= ' AND ('.$find_setCond.')';
            //     }
            //     else{
            //         $wherFindSet .= ' AND '.$find_setCond.'';
            //     }
            //     $wherQuery .= ' AND ( CASE 
            //                     WHEN (promo_list.conflict_promos !="" '.$wherFindSet.' ) THEN
            //                     0 ELSE 1 
            //                     END ) ';
            // }                     
                                 
        }
        /*if(empty($promo_type)) {
              return "empty";
        } else {
            return "not empty";
        }*/
 
        if (!empty($promo_type)) { 
            if(is_array($promo_type)) {
              $promo_type = join(",",$promo_type);
            }
            
            $wherQuery .= ' AND promo_list.promo_type IN ('.$promo_type.') ';
        } 
        
        if (!empty($promo_text)) { 
            $wherQuery .= ' AND promo_list.promo_name LIKE "%'.$promo_type.'%" ';
        }

        $sqlCount = 'SELECT COUNT(*) FROM promo_list,promo_list_display_language WHERE 
            ( CASE 
                WHEN promo_list.promo_end_time != 0 THEN   promo_list.promo_start_time <= '.$time.' AND (promo_list.promo_end_time) >= '.$time.'  
                WHEN promo_list.promo_end_time = 0 THEN  promo_list.promo_start_time <= '.$time.' 
                ELSE 1
            END ) AND 
            ( CASE 
                WHEN promo_list.display_time_type = 2 THEN   promo_list.manual_start_status = 1  
               WHEN promo_list.display_time_type = 3 THEN  
                    ( CASE 
                        WHEN promo_list.display_end_time != 0 THEN   promo_list.display_start_time <= '.$time.' AND (promo_list.display_end_time) >= '.$time.' 
                        WHEN promo_list.display_end_time = 0 THEN  promo_list.display_start_time <= '.$time.' 
                        ELSE 1
                    END )
                ELSE 1
            END ) '.$wherQuery.' ';

        $this->query($sqlCount);

        $totalCount = $this->getValue();

        return $totalCount;
    }  


    /**
     * get all promo list for display
     *
     * @param int $id
     * @return boolean $status
     */
    public function getAllActivePromo($post)
    {   

        $language   = isset($post['language'])?$post['language']:'';
        $promo_type = isset($post['promo_type'])?$post['promo_type']:'';
        $device     = isset($post['device'])?$post['device']:'1';
        $page       = isset($post['page'])?$post['page']:'';
        $perPage    = isset($post['perPage'])?$post['perPage']:'';
        $page       = ((int)$page > 0 )? $page : 1;
        $perPage    = ((int)$perPage > 0 )? $perPage : 0;
        $pageStart  = ($page - 1) * $perPage;
        $time       = time();

        
        $wherQuery  = ' AND  promo_list.status = 1 and promo_list.id = promo_list_display_language.promo_list_id and delete_status=0 and promo_list_display_language.language_code = "'.$language.'" ';

        if(!empty($device)){
            $wherQuery .= '  AND ( CASE WHEN promo_list.device != "" THEN FIND_IN_SET('.$device.',promo_list.device) ELSE 1 END ) ';
        }

        if(!empty($post['player_id'])){

            $player_id = $post['player_id'];

            $user = ( isset($post['user_info']) && !empty($post['user_info']) ) ? $post['user_info'] : $this->getUserInfo($player_id);

            $affiliate_id = $user['affiliate_id'] ?? 0;
            $player_id = $user['id'] ?? 0;
            $player_group_id = $user['player_group_id'] ?? 0;
            $mobile_verification_status = $user['mobile_verification_status'] ?? 0;
            $is__security_pin = $user['is__security_pin'] ?? 0;
            $is__bank = $user['is__bank'] ?? 0;
            $promo_block = $user['promo_block'] ?? 0;

            if(!empty($promo_block)){
               $wherQuery .= ' AND 0 ';
            }

            /*$wherQuery .= ' AND apply_type = 1 AND 
                            ( CASE 
                                WHEN promo_list.target_type = 2  THEN FIND_IN_SET(\''.$affiliate_id.'\', promo_list.target_list)
                                WHEN promo_list.target_type = 3  THEN FIND_IN_SET(\''.$player_id.'\', promo_list.target_list) 
                                ELSE 1 END )';*/

            $wherQuery .= ' AND 
                            ( CASE 
                                WHEN promo_list.target_type = 2  THEN FIND_IN_SET(\''.$affiliate_id.'\', promo_list.target_list)
                                WHEN promo_list.target_type = 3  THEN FIND_IN_SET(\''.$player_id.'\', promo_list.target_list) 
                                ELSE 1 END )';
                                                    
            
            if(!empty($player_group_id)){
                $wherQuery .= ' AND 
                            ( CASE 
                                WHEN promo_list.member_level_list != ""   THEN FIND_IN_SET(\''.$player_group_id.'\', promo_list.member_level_list)
                                ELSE 1 END )
                                ';
            }

            // $wherQuery .= ' AND 
            //                 ( CASE 
            //                     WHEN (promo_list.verify_details != "" AND FIND_IN_SET(1, promo_list.verify_details) AND '.$mobile_verification_status.' != 1 )
            //                        THEN  0 ELSE 1  
            //                 END )';

            // $wherQuery .= ' AND 
            //                 ( CASE 
            //                     WHEN (promo_list.verify_details != "" AND FIND_IN_SET(2, promo_list.verify_details) AND   '.$is__security_pin.' = 0  )
            //                        THEN  0 ELSE 1  
            //                 END )';
            // $wherQuery .= ' AND 
            //                 ( CASE 
            //                     WHEN (promo_list.verify_details != "" AND FIND_IN_SET(3, promo_list.verify_details) AND '.$is__bank.' = 0)
            //                        THEN  0 ELSE 1  
            //                 END )';
                                        
            // $getAllAppliedPromos = $this->callSql("SELECT GROUP_CONCAT(DISTINCT promo_list_id SEPARATOR ',') FROM promo_activity_list WHERE player_id =".$player_id." ", "value");
            // $wherFindSet = '';
            // if(!empty($getAllAppliedPromos)){
            //     $explodeArr = explode(',',$getAllAppliedPromos);
            //     $find_setCond = '';
            //     foreach ($explodeArr as $key => $value) {
            //         if(!empty($find_setCond)){
            //             $find_setCond .= ' OR FIND_IN_SET('.$value.',promo_list.conflict_promos) ';

            //         }
            //         else{
            //             $find_setCond .= ' FIND_IN_SET('.$value.',promo_list.conflict_promos) ';

            //         }
            //     }
            //     if(count($explodeArr) > 1){
            //         $wherFindSet .= ' AND ('.$find_setCond.')';
            //     }
            //     else{
            //         $wherFindSet .= ' AND '.$find_setCond.'';
            //     }
            //     $wherQuery .= ' AND ( CASE 
            //                     WHEN (promo_list.conflict_promos !="" '.$wherFindSet.' ) THEN
            //                     0 ELSE 1 
            //                     END ) ';
            // }                    
                                 
        }

        // if(!empty($game_type)){ 
        //     $wherQuery .= ' AND FIND_IN_SET("'.$game_type.'",promo_list.game_types) ';
        // }
        if(!empty($promo_type)){ 
            if(is_array($promo_type)) {
              $promo_type = join(",",$promo_type);
            }
            if($promo_type==9) {
                $wherQuery .= ' AND promo_list.promo_type =6 AND promo_list.promo_sub_type=1 ';     
            }else{
                $wherQuery .= ' AND promo_list.promo_type IN ('.$promo_type.') ';    
            }

           
        }
         
        //  if(!empty($promo_text)){ 
        //     $wherQuery .= ' AND promo_list.promo_name LIKE "%'.$promo_type.'%" ';
        // }

        $query  = 'SELECT ';
        $query .= '     promo_list.id,
                        promo_list.promo_name,
                        promo_list.promo_start_time,
                        promo_list.promo_end_time,
                        promo_list.promo_sub_type,
                        promo_list.promo_type,
                        promo_list.promo_name,
                        promo_list.is_hot_promo,
                        promo_list.apply_type,
                        promo_list.game_selection_type,
                        promo_list.game_vendors_wallet_id,
                        promo_list_display_language.banner_1,
                        promo_list_display_language.banner_2,
                        promo_list_display_language.description_web,
                        promo_list_display_language.description_h5,
                        promo_list_display_language.description_app,
                        promo_list_display_language.icon,
                        CASE
                            WHEN promo_list.display_type =  2 THEN promo_list.display_type_url
                            ELSE ""
                        END AS promo_url
                        ';
        $query .= ' FROM promo_list,promo_list_display_language WHERE  
            ( CASE 
                WHEN (promo_list.display_time_type = 1) THEN 
                ( CASE 
                    WHEN promo_list.promo_end_time != 0 THEN   promo_list.promo_start_time <= '.$time.' AND (promo_list.promo_end_time) >= '.$time.'  
                    WHEN promo_list.promo_end_time = 0 THEN  promo_list.promo_start_time <= '.$time.' 
                    ELSE 1
                END )
                WHEN promo_list.display_time_type = 2 THEN   promo_list.manual_start_status = 1  
                WHEN promo_list.display_time_type = 3 THEN  
                    ( CASE 
                        WHEN promo_list.display_end_time != 0 THEN   promo_list.display_start_time <= '.$time.' AND (promo_list.display_end_time) >= '.$time.' 
                        WHEN promo_list.display_end_time = 0 THEN  promo_list.display_start_time <= '.$time.' 
                        ELSE 1
                    END ) 
                ELSE 1
            END ) '.$wherQuery.' ORDER BY promo_list.sort_order ASC ';

        if(!isset($post['limit']) && $perPage > 0)
        {
          $query .= ' LIMIT '.$pageStart.','.$perPage.'';  
            
        } 
                
        $this->query($query);

        $dataList = $this->resultset();
        
        return $dataList;
    }



 

      /**
     * get max bonus
     *
     * @param int $id
     * @return boolean $status
     */
    public function getMaxPromoBonus($promoId)
    {
        $wherQuery = '';
        if(!empty($promoId)){
            $wherQuery .= ' WHERE promo_list_id = '.$promoId;
        }
        $dataArray      = [];
        $query          = 'SELECT MAX(bonus_amount)';
        $query         .= ' FROM promo_condition_list '.$wherQuery;
        $this->query($query);
        $dataList = $this->getValue();
        return $dataList;
    } 

    public function getUserInfo($user_id)
    {
        $result = $request = array();

        $request[0]['serviceName'] = MICRO_SERVICES['player'];
        $request[0]['command'] = 'player/collectionWithBank';
        $request[0]['param']['id'] = $user_id;
        $request[0]['param']['start'] = 0;
        $request[0]['param']['length'] = 1;
        $request[0]['param']['select'] = ['id', 'username', 'full_name', 'affiliate_id', 'status', 'player_vip_id', 'player_group_id', 'created_ip', 'mobile_verification_status', 'is__security_pin', 'is__bank','player_group.promo_block'];

        $res = Raise::callApi($request);
        
        return $res[0]['data']['data'][0] ?? array();
    }


      /**
     * get promo details
     *
     * @param int $id
     * @return boolean $status
     */
    public function getPromoDetails($request)
    {
        $promoId    = $request['promoId'];
        $language   = $request['language'];
        $wherQuery  = '';
        $viplevel   = '1';
        if(!empty($promoId)){
            $wherQuery .= ' WHERE pl.promo_list_id = p.id  and p.id = '.$promoId.'  AND pl.language_code = "'.$language.'" AND p.status = 1  ';
        }
        
        if(!empty($request['player_id'])){
            // $param       = ["player_group_id"];
            // $microArr [] = [
            //       'serviceName' => MICRO_SERVICES['player'],
            //       'command'     => 'player/collectionWithBank',
            //       //'param'       => $param,
            //       "param"=> [
            //       "select"=> ["id","player_vip_id"],
            //       "id"=> $request['player_id']
            // ]
                 
            // ];
            // if (!empty($microArr)) {
            //     $result_arr   = Raise::callApi($microArr);

                


                
            //     $result       = $result_arr[0];
            //     if($result['success']) {
            //         $viplevel = $result['data']['data'][0]['player_vip_id'];
            //     } 
                
            // }  

            $user = $this->getUserInfo($request['player_id']);

            $viplevel = $user['player_vip_id'];
            if($viplevel != ""){
                $customQuery = ',CASE WHEN (p.promo_type = 6 && promo_sub_type = 0) THEN (SELECT TRIM(BOTH \'"\' FROM custom_label_setting) FROM promo_condition_list WHERE promo_list_id = '.$promoId.' and vip_level_id = '.$viplevel.' ) ELSE "" END as promojson';
            }
            else{
                $customQuery = '';
            }

            
        }
        else{
        $customQuery = ''; 
        }
        $dataArray          = [];
         $query  = 'SELECT ';
        $query .= '     pl.promo_list_id,
                        pl.title,
                        pl.icon,
                        pl.banner_1,
                        pl.banner_2,
                        p.apply_type,
                        pl.description_h5,
                        pl.description_web,
                        pl.description_app,
                        p.promo_start_time,
                        p.promo_end_time,
                        p.promo_sub_type,
                        p.promo_type,
                        p.game_selection_type,
                        p.game_vendors_wallet_id,
                        p.game_vendors,
                        CASE
                            WHEN p.display_type =  2 THEN p.display_type_url
                            ELSE ""
                        END AS promo_url,
                        p.max_apply_count'.$customQuery.'
                        ';
        $query .= ' FROM promo_list as p,promo_list_display_language as pl '.$wherQuery.' ';
        $this->query($query);
        $dataList = $this->resultset();
        return $dataList;
    }

    /**
     * get maximum apply for a particular promo
     *
     * @param int $id
     * @return boolean $status
     */
    public function getMaxiumApply($playerId,$promoId)
    {
        $wherQuery = '';
        if(!empty($promoId)){
            $wherQuery .= ' AND player_id = "'.$playerId.'" and  promo_list_id = "'.$promoId.'"  ';
        }
        $query  = 'SELECT * FROM ( SELECT count(*) as runningtotal FROM promo_activity_list  WHERE promo_status = 1  '.$wherQuery.'   ) as A,  ( SELECT count(*) as totalapply FROM promo_activity_list WHERE 1 '.$wherQuery.'  ) as B ';
        $this->query($query);
        $dataList = $this->resultset();
        return $dataList;
    }

    public function getDefaultCustomJson($promoId){
        // return $promoId;
       $query = "SELECT TRIM(BOTH '\"' FROM custom_label_setting) FROM promo_condition_list WHERE promo_list_id = ".$promoId." and vip_level_id = 0  LIMIT 1";
        $this->query($query);
        $dataList = $this->getValue();
        return $dataList;

    }


    public function getCheckEligibleUser($post)
    {
        $device     = isset($post['device'])?$post['device']:'1';
        $promo_id     = isset($post['promoId'])?$post['promoId']:'0';
        $player_id     = isset($post['player_id'])?$post['player_id']:'0';
        $wherQuery  = ' AND promo_list.id = '.$promo_id.' AND promo_list.status = 1  and delete_status=0  ';
         if(!empty($device)){
                    $wherQuery .= ' AND (CASE WHEN promo_list.device != "" THEN FIND_IN_SET('.$device.',promo_list.device) ELSE 1 END ) ';
                }

        if(!empty($post['player_id'])){
            $user = $this->getUserInfo($player_id);
            $affiliate_id = $user['affiliate_id'] ?? 0;
            $player_id = $user['id'] ?? 0;
            $player_group_id = $user['player_group_id'] ?? 0;
            $mobile_verification_status = $user['mobile_verification_status'] ?? 0;
            $is__security_pin = $user['is__security_pin'] ?? 0;
            $is__bank = $user['is__bank'] ?? 0;
            $promo_block = $user['promo_block'] ?? 0;

            if(!empty($promo_block)){
               $wherQuery .= ' AND 0 ';
            }

            $wherQuery .= ' AND 
                            ( CASE 
                                WHEN promo_list.target_type = 2  THEN FIND_IN_SET(\''.$affiliate_id.'\', promo_list.target_list)
                                WHEN promo_list.target_type = 3  THEN FIND_IN_SET(\''.$player_id.'\', promo_list.target_list) 
                                ELSE 1 END )';
            
            if(!empty($player_group_id)){
                $wherQuery .= ' AND 
                            ( CASE 
                                WHEN promo_list.member_level_list != ""   THEN FIND_IN_SET(\''.$player_group_id.'\', promo_list.member_level_list)
                                ELSE 1 END )
                                ';
            }


            // $getAllAppliedPromos = $this->callSql("SELECT GROUP_CONCAT(DISTINCT promo_list_id SEPARATOR ',') FROM promo_activity_list WHERE player_id =".$player_id." ", "value");
            //     $wherFindSet = '';
            //     if(!empty($getAllAppliedPromos)){
            //         $explodeArr = explode(',',$getAllAppliedPromos);
            //         $find_setCond = '';
            //         foreach ($explodeArr as $key => $value) {
            //             if(!empty($find_setCond)){
            //                 $find_setCond .= ' OR FIND_IN_SET('.$value.',promo_list.conflict_promos) ';

            //             }
            //             else{
            //                 $find_setCond .= ' FIND_IN_SET('.$value.',promo_list.conflict_promos) ';

            //             }
            //         }
            //         if(count($explodeArr) > 1){
            //             $wherFindSet .= ' AND ('.$find_setCond.')';
            //         }
            //         else{
            //             $wherFindSet .= ' AND '.$find_setCond.'';
            //         }
            //         $wherQuery .= ' AND ( CASE 
            //                         WHEN (promo_list.conflict_promos !="" '.$wherFindSet.' ) THEN
            //                         0 ELSE 1 
            //                         END ) ';
            //     }  



        }
$time = time();
$query  = 'SELECT ';
        $query .= '  count(id)
                        ';

        $query .= ' FROM promo_list WHERE 
            ( CASE 
             WHEN promo_list.display_time_type = 1 THEN  
                    ( CASE WHEN promo_list.promo_end_time != 0 THEN promo_list.promo_start_time <= '.$time.' AND (promo_list.promo_end_time) >= '.$time.' 
                                            WHEN promo_list.promo_end_time = 0 THEN  promo_list.promo_start_time <= '.$time.' 
                                            ELSE 1
                    END )  
                WHEN promo_list.display_time_type = 2 THEN   promo_list.manual_start_status = 1  
                WHEN promo_list.display_time_type = 3 THEN  
                    ( CASE 
                        WHEN promo_list.display_end_time != 0 THEN   promo_list.display_start_time <= '.$time.' AND (promo_list.display_end_time) >= '.$time.' 
                        WHEN promo_list.display_end_time = 0 THEN  promo_list.display_start_time <= '.$time.' 
                        ELSE 1
                    END )
                ELSE 1
            END ) '.$wherQuery.'  ';

       $result = $this->callSql($query,'value');

        
        return $result;

}

    /**
     * get maximum apply for a particular promo
     *
     * @param int $id
     * @return boolean $status
     */
    public function getPromoLoginList($prms)
    {

        //Redis publish
        $player_id = $prms['player_id'] ?? 0;

        if ($player_id > 0) {
            H::redisPublish('treasureBox', ['player_id' => $player_id], "19_promo");
        }
        //End Redis Publish

        $data    = [];
        $limit   = $prms['perPage'] ?? 0;
        $offset  = $prms['page'] ?? 0;
        $page       = ((int)$offset > 0 )? $offset : 1;
        $perPage    = ((int)$limit > 0 )? $limit : 0;
        $pageStart  = ($page - 1) * $perPage;

        $language= $prms['language'];
        $viplevel = 0;
        $limitDt = '';
        if(!empty($perPage)){
            $limitDt = ' LIMIT '.$pageStart.','.$perPage.' ';
        }
        $device  = $prms['device']??3;
        $orderDt = ' ORDER BY promo_list.sort_order ASC';
        $time    = time();
        $join    = ' LEFT JOIN promo_list_display_language ON promo_list.id = promo_list_display_language.promo_list_id';
        $wherQry = '  WHERE (CASE WHEN promo_type = 6 THEN promo_sub_type != 1 ELSE 1 END )  and  
                                  promo_list_display_language.language_code = "'.$language.'" AND status = 1 AND delete_status = 0 AND apply_type = 1  AND 
                                  FIND_IN_SET('.$device.',promo_list.device) AND 
                                    ( CASE 
                WHEN promo_list.display_time_type = 1 THEN  
                    ( CASE WHEN promo_list.promo_end_time != 0 THEN promo_list.promo_start_time <= '.$time.' AND (promo_list.promo_end_time) >= '.$time.' 
                                            WHEN promo_list.promo_end_time = 0 THEN  promo_list.promo_start_time <= '.$time.' 
                                            ELSE 1
                    END )                 
                WHEN promo_list.display_time_type = 2 THEN   promo_list.manual_start_status = 1  
                WHEN promo_list.display_time_type = 3 THEN  
                    ( CASE 
                        WHEN promo_list.display_end_time != 0 THEN   promo_list.display_start_time <= '.$time.' AND (promo_list.display_end_time) >= '.$time.' 
                        WHEN promo_list.display_end_time = 0 THEN  promo_list.display_start_time <= '.$time.' 
                        ELSE 1
                    END ) 
                ELSE 1
            END ) ';
            if(!empty($prms['player_id'])){
            $user_id = $prms['player_id'];
            $user = $this->getUserInfo($user_id);
            // $user = array('affiliate_id'=>1,'id'=>7,'player_group_id'=>1,'mobile_verification_status'=>1,'is__security_pin'=>0,'is__bank'=>1);
            $affiliate_id = $user['affiliate_id'];
            $player_id = $user['id'];
            $player_group_id = $user['player_group_id'];
            $mobile_verification_status = $user['mobile_verification_status'];
            $is__security_pin = $user['is__security_pin'];
            $is__bank = $user['is__bank'];

             $promo_block = $user['promo_block'];
            if(!empty($promo_block)){
               $wherQry .= ' AND 0 ';
            }

            $wherQry .= ' AND apply_type = 1 AND 
                            ( CASE 
                                WHEN promo_list.target_type = 2  THEN FIND_IN_SET(\''.$affiliate_id.'\', promo_list.target_list)
                                WHEN promo_list.target_type = 3  THEN FIND_IN_SET(\''.$player_id.'\', promo_list.target_list) 
                                ELSE 1 END )';
            
            if(!empty($player_group_id)){
                $wherQry .= ' AND 
                            ( CASE 
                                WHEN promo_list.member_level_list != ""   THEN FIND_IN_SET(\''.$player_group_id.'\', promo_list.member_level_list)
                                ELSE 1 END )
                                ';
            }

            // $wherQry .= ' AND 
            //                 ( CASE 
            //                     WHEN (promo_list.verify_details != "" AND FIND_IN_SET(1, promo_list.verify_details) AND '.$mobile_verification_status.' != 1 )
            //                        THEN  0 ELSE 1  
            //                 END )';

            // $wherQry .= ' AND 
            //                 ( CASE 
            //                     WHEN (promo_list.verify_details != "" AND FIND_IN_SET(2, promo_list.verify_details) AND   '.$is__security_pin.' = 0  )
            //                        THEN  0 ELSE 1  
            //                 END )';
            // $wherQry .= ' AND 
            //                 ( CASE 
            //                     WHEN (promo_list.verify_details != "" AND FIND_IN_SET(3, promo_list.verify_details) AND '.$is__bank.' = 0)
            //                        THEN  0 ELSE 1  
            //                 END )';
                                        
            // $getAllAppliedPromos = $this->callSql("SELECT GROUP_CONCAT(DISTINCT promo_list_id SEPARATOR ',') FROM promo_activity_list WHERE player_id =".$user_id." ", "value");
            // $wherFindSet = '';
            // if(!empty($getAllAppliedPromos)){
            //     $explodeArr = explode(',',$getAllAppliedPromos);
            //     $find_setCond = '';
            //     foreach ($explodeArr as $key => $value) {
            //         if(!empty($find_setCond)){
            //             $find_setCond .= ' OR FIND_IN_SET('.$value.',promo_list.conflict_promos) ';

            //         }
            //         else{
            //             $find_setCond .= ' FIND_IN_SET('.$value.',promo_list.conflict_promos) ';

            //         }
            //     }
            //     if(count($explodeArr) > 1){
            //         $wherFindSet .= ' AND ('.$find_setCond.')';
            //     }
            //     else{
            //         $wherFindSet .= ' AND '.$find_setCond.'';
            //     }
            //     $wherQry .= ' AND ( CASE 
            //                     WHEN (promo_list.conflict_promos !="" '.$wherFindSet.' ) THEN
            //                     0 ELSE 1 
            //                     END ) ';
            // }                    
                                 
        }

        $select =  'promo_list.id,
                    promo_list.promo_name,
                    promo_list.promo_type,
                    promo_list.promo_sub_type,
                    promo_list.max_apply_count,
                    promo_list.game_vendors,
                    promo_list.game_selection_type as all_games_promo,
                    promo_list.game_vendors_wallet_id as locked_vendor,
                    CASE
                        WHEN promo_list.display_type =  2 THEN promo_list.display_type_url
                        ELSE ""
                    END AS promo_url
                    ';



        if($device == 3){

        $select .=  ',promo_list_display_language.description_app as description';
        }
        else if($device == 2){
        $select .=  ',promo_list_display_language.description_h5';

        }
        else if($device == 1){
        $select .=  ',promo_list_display_language.description_web';

        }
        $select .=  ',promo_list_display_language.banner_1,
                    promo_list_display_language.banner_2,promo_list_display_language.icon';


        $query  = 'SELECT count(promo_list.id) FROM '.$this->tableName.' '.$join.'' .$wherQry;
        $this->query($query);

        $data['recordsFiltered'] = (int) $this->getValue();
        $data['perPage']         = $limit;
        $queryList = "SELECT " .$select . " FROM " . $this->tableName ." ".$join ." " .$wherQry." ".$orderDt. " ". $limitDt." "; 
        $this->query($queryList);

         $data['recordsList'] = $this->resultset();
            // foreach ($data['recordsList'] as $index => $value) {
            //     foreach ($value as $key => $kValue) {
            //         if ($key == 'promo_name') {
            //             $promo_name_arr = json_decode($kValue,true);
            //             $data['recordsList'][$index]['promo_name'] = $promo_name_arr[$language] ?? '';
            //         }
            //     }
            // }
        return $data;
    }

    public function getcustomPromoDetail($viplevel,$promoId){
          $queryList = "SELECT TRIM(BOTH '\"' FROM custom_label_setting) FROM promo_condition_list WHERE promo_list_id = '".$promoId."' and vip_level_id = '".$viplevel."' "; 
        $this->query($queryList);
        $customval =  $this->getValue();
        if(empty($customval)){
            $query = "SELECT TRIM(BOTH '\"' FROM custom_label_setting) FROM promo_condition_list WHERE promo_list_id = ".$promoId." and vip_level_id = 0  LIMIT 1";
            $this->query($query);
            $customval = $this->getValue();
        }
        return $customval;
    }


    public function getPromoLoginListWeb($prms)
    {

        //Redis publish
        $player_id = $prms['player_id'] ?? 0;

        if ($player_id > 0) {
            H::redisPublish('treasureBox', ['player_id' => $player_id], "19_promo");
        }
        //End Redis Publish

        $data    = [];
        // $limit   = $prms['perPage'] ?? 10;
        // $offset  = $prms['page'] ?? 0;
        // $page       = ((int)$offset > 0 )? $offset : 1;
        // $perPage    = ((int)$limit > 0 )? $limit : 10;
        // $pageStart  = ($page - 1) * $perPage;

        $language= $prms['language'];
        $promo_title= isset($prms['promo_title'])?$prms['promo_title']:'';
        $promo_type= isset($prms['promo_type'])?$prms['promo_type']:'';
        $player_id= isset($prms['player_id'])?$prms['player_id']:'';
        // $limitDt = ' LIMIT '.$pageStart.','.$perPage.' ';
        $device  = $prms['device']??3;
        $orderDt = ' ORDER BY promo_list.sort_order ASC';
        $time    = time();
        $join    = ' LEFT JOIN promo_list_display_language ON promo_list.id = promo_list_display_language.promo_list_id';
        $wherQry = '  WHERE (CASE WHEN promo_type = 6 THEN promo_sub_type != 1 ELSE 1 END )  and  
                                  promo_list_display_language.language_code = "'.$language.'" AND status = 1  AND delete_status = 0  AND apply_type = 1  AND 
                                  FIND_IN_SET('.$device.',promo_list.device) AND  
                                    ( CASE 
                WHEN promo_list.display_time_type = 1 THEN  
                    ( CASE WHEN promo_list.promo_end_time != 0 THEN promo_list.promo_start_time <= '.$time.' AND (promo_list.promo_end_time) >= '.$time.' 
                                            WHEN promo_list.promo_end_time = 0 THEN  promo_list.promo_start_time <= '.$time.' 
                                            ELSE 1
                    END )  
                WHEN promo_list.display_time_type = 2 THEN   promo_list.manual_start_status = 1  
                WHEN promo_list.display_time_type = 3 THEN  
                    ( CASE 
                        WHEN promo_list.display_end_time != 0 THEN   promo_list.display_start_time <= '.$time.' AND (promo_list.display_end_time) >= '.$time.' 
                        WHEN promo_list.display_end_time = 0 THEN  promo_list.display_start_time <= '.$time.' 
                        ELSE 1
                    END ) 
                ELSE 1
            END ) ';
            if(!empty($prms['player_id'])){
            $user_id = $prms['player_id'];
            $user = $this->getUserInfo($user_id);
            // $user = array('affiliate_id'=>1,'id'=>7,'player_group_id'=>1,'mobile_verification_status'=>1,'is__security_pin'=>0,'is__bank'=>1);
            $affiliate_id = $user['affiliate_id'];
            $player_id = $user['id'];
            $player_group_id = $user['player_group_id'];
            $mobile_verification_status = $user['mobile_verification_status'];
            $is__security_pin = $user['is__security_pin'];
            $is__bank = $user['is__bank'];
            $promo_block = $user['promo_block'];
            if(!empty($promo_block)){
               $wherQry .= ' AND 0 ';
            }
            $wherQry .= ' AND apply_type = 1 AND 
                            ( CASE 
                                WHEN promo_list.target_type = 2  THEN FIND_IN_SET(\''.$affiliate_id.'\', promo_list.target_list)
                                WHEN promo_list.target_type = 3  THEN FIND_IN_SET(\''.$player_id.'\', promo_list.target_list) 
                                ELSE 1 END )';
            
            if(!empty($player_group_id)){
                $wherQry .= ' AND 
                            ( CASE 
                                WHEN promo_list.member_level_list != ""   THEN FIND_IN_SET(\''.$player_group_id.'\', promo_list.member_level_list)
                                ELSE 1 END )
                                ';
            }

            // $wherQry .= ' AND 
            //                 ( CASE 
            //                     WHEN (promo_list.verify_details != "" AND FIND_IN_SET(1, promo_list.verify_details) AND '.$mobile_verification_status.' != 1 )
            //                        THEN  0 ELSE 1  
            //                 END )';

            // $wherQry .= ' AND 
            //                 ( CASE 
            //                     WHEN (promo_list.verify_details != "" AND FIND_IN_SET(2, promo_list.verify_details) AND   '.$is__security_pin.' = 0  )
            //                        THEN  0 ELSE 1  
            //                 END )';
            // $wherQry .= ' AND 
            //                 ( CASE 
            //                     WHEN (promo_list.verify_details != "" AND FIND_IN_SET(3, promo_list.verify_details) AND '.$is__bank.' = 0)
            //                        THEN  0 ELSE 1  
            //                 END )';
                                        
            // $getAllAppliedPromos = $this->callSql("SELECT GROUP_CONCAT(DISTINCT promo_list_id SEPARATOR ',') FROM promo_activity_list WHERE player_id =".$user_id." ", "value");
            // $wherFindSet = '';
            // if(!empty($getAllAppliedPromos)){
            //     $explodeArr = explode(',',$getAllAppliedPromos);
            //     $find_setCond = '';
            //     foreach ($explodeArr as $key => $value) {
            //         if(!empty($find_setCond)){
            //             $find_setCond .= ' OR FIND_IN_SET('.$value.',promo_list.conflict_promos) ';

            //         }
            //         else{
            //             $find_setCond .= ' FIND_IN_SET('.$value.',promo_list.conflict_promos) ';

            //         }
            //     }
            //     if(count($explodeArr) > 1){
            //         $wherFindSet .= ' AND ('.$find_setCond.')';
            //     }
            //     else{
            //         $wherFindSet .= ' AND '.$find_setCond.'';
            //     }
            //     $wherQry .= ' AND ( CASE 
            //                     WHEN (promo_list.conflict_promos !="" '.$wherFindSet.' ) THEN
            //                     0 ELSE 1 
            //                     END ) ';
            // }                    
                                 
        }

        $select =  'promo_list.id as id,
                    promo_list.promo_name,
                    promo_list.promo_type,
                    promo_list.game_selection_type as all_games_promo,
                    promo_list.game_vendors_wallet_id as locked_vendor,
                    promo_list_display_language.title,
                    promo_list.display_type as display_type,
                    promo_list.display_type_url as display_type_url,
                    promo_list.display_type_new_tab as display_type_new_tab';
        // if($device == 3){

        // $select .=  ',promo_list_display_language.description_app';
        // }
        // else if($device == 2){
        // $select .=  ',promo_list_display_language.description_h5';

        // }
        // else if($device == 1){
        // $select .=  ',promo_list_display_language.description_web';

        // }
        if(!empty($promo_title)){
          $wherQry .=  ' AND promo_list_display_language.title  LIKE "%'.$prms['promo_title'].'%" ';
        }
        if($promo_type != ''){
          $wherQry .=  ' AND promo_list.promo_type  = '.$prms['promo_type'].' ';
        }
        if($player_id != ''){

        $allmyprom_count = $this->query("SELECT count(id) FROM promo_activity_list WHERE player_id =  '$player_id' and promo_type NOT IN (7,8)");
        $data['mypromoCount'] = (int) $this->getValue();

        $this->query("SELECT id,sign_days_completed_list,sign_days_limit FROM promo_activity_list WHERE player_id = '$player_id' AND promo_type = '7' AND promo_status = 1 LIMIT 1");
        $data['signinapplied'] = $this->resultset();

        }
        $select .=  ',promo_list_display_language.banner_1,
                    promo_list_display_language.banner_2,promo_list_display_language.icon';
        $query  = 'SELECT count(promo_list.id) FROM '.$this->tableName.' '.$join.'' .$wherQry;
        $this->query($query);

        $data['recordsFiltered'] = (int) $this->getValue();
        $queryList = "SELECT " .$select . " FROM " . $this->tableName ." ".$join ." " .$wherQry." ".$orderDt. " "; 
        $this->query($queryList);

         $data['recordsList'] = $this->resultset();

            foreach ($data['recordsList'] as $index => $value) {
                foreach ($value as $key => $kValue) {
                    if ($key == 'promo_name') {
                        $promo_name_arr = json_decode($kValue,true);
                        $data['recordsList'][$index]['promo_name'] = !empty($promo_name_arr[$language])?htmlentities($promo_name_arr[$language],ENT_QUOTES):'';
                    }
                    if($key == 'title'){
                         $data['recordsList'][$index]['title'] = !empty($kValue)?htmlentities($kValue,ENT_QUOTES):'';
                    }
                }
            }
        return $data;
    }


 /**
     * get signindetails
     *
     * @param int $id
     * @return boolean $status
     */
    public function getSigninDetailsPromo($player_id)
    {
        if ($player_id <= 0) {
            return ['mypromoCount' => 0, 'mypromocollectcnt' => 0, 'signinapplied' => []];
        }

        $data = [];
        $allmyprom_count = $this->query("SELECT count(id) FROM promo_activity_list WHERE player_id =  '$player_id' and promo_type NOT IN (7,8)");
        $data['mypromoCount'] = (int) $this->getValue();

        
         $allcollectpromo_count = $this->callSql('SELECT * FROM promo_activity_list WHERE player_id =  \''.$player_id.'\' and promo_type NOT IN (7,8) ','rows');
         $collectcnt =0;
         foreach ($allcollectpromo_count as $key => $value) {
             if ($value['promo_status'] == '1') {
                if (in_array($value['promo_type'],[1,5]) && $value['paid_status'] == 0 && $value['payout_collection_type'] == 1 && $value['payout_collection_request_status'] == 0) {
                        $collectcnt++;
                    }

                
                else {
                    if($value['payout_collection_type'] == 1) {
                        $promo_activity_id = $value['id'];
                        $pending_collect_count = $this->callSql("SELECT COUNT(*) FROM promo_activity_rule_applied WHERE promo_activity_list_id = '".$promo_activity_id."' AND status = 2 AND bonus_collect_request_status = 0 AND bonus_paid_status = 0  ","value");
                        if ($pending_collect_count > 0) {
                            $collectcnt++;
                        }
                    }
                }
            }
            else{
                if ($value['promo_status'] == 2 && $value['paid_status'] == 0 && $value['promo_type'] == 5 && $value['payout_collection_type'] == 1 && $value['payout_collection_request_status'] != 1 ) {
                            $collectcnt++;
                }
            }
         }



        $data['mypromocollectcnt'] = $collectcnt;


        $this->query("SELECT id,sign_days_completed_list,sign_days_limit FROM promo_activity_list WHERE player_id = '$player_id' AND promo_type = '7' AND promo_status = 1 LIMIT 1");
        $signIndetails = $this->resultset();

        /* to get the max consecutive days completed */
        $getmax_grp = 0;
        if(!empty($signIndetails[0]['sign_days_completed_list'])){

          $signcom = $signIndetails[0]['sign_days_completed_list'];
          $explode_arr = explode(',', $signcom);
          $cons_grp = $this->group_consecutive_num($explode_arr);
          $getmax_grp = max($cons_grp);
        }
        if(!empty($signIndetails)){
            $signIndetails[0]['sign_days_completed_count'] = $getmax_grp; 
        }
        /* to get the max consecutive days completed */




        $data['signinapplied'] = $signIndetails;
        return $data;
    }


    public function group_consecutive_num($array) {
        $return_arr  = array();
        $temp_arr = array();
          foreach($array as $val) {
            if(next($array) == ($val + 1))
            $temp_arr[] = $val;
            else
            if(count($temp_arr) > 0) {
            $temp_arr[] = $val;
            $return_arr[]  = (end($temp_arr)-$temp_arr[0])+1;
            $temp_arr   = array();
            }
            else
            $return_arr[] = 1;
          }
        return $return_arr;
      }
    

    /**
     * get all applied promo details
     *
     * @param int $id
     * @return boolean $status
     */
    public function getAppiedDetailsPromo($playerId,$promoId)
    {
        $wherQuery = '';
        if(!empty($promoId)){
            $wherQuery .= ' WHERE player_id = "'.$playerId.'" and  promo_list_id = "'.$promoId.'"  ';
        }
        $query  = 'SELECT *  FROM promo_activity_list '.$wherQuery.' ORDER BY promo_activity_list.id DESC LIMIT 3';
        // $this->query($query);
        $dataList = $this->callSql($query,"rows");
            $collect_status_text = '';
        // return $dataList;
        foreach ($dataList as $key => $value) {
            $promo_list_id = $value['promo_list_id'];
            $promo_list = $this->callSql("SELECT promo_name,promo_start_time,promo_end_time FROM promo_list WHERE id = $promo_list_id ","row");
            $dataList[$key]['promo_name'] =  $promo_list['promo_name'];
            $dataList[$key]['promo_end_time'] =  $promo_list['promo_end_time'];
            $dataList[$key]['promo_start_time'] =  $promo_list['promo_start_time'];
            // return $dataList;
            if ($value['promo_status'] == '1') {

              if (in_array($value['promo_type'],[1,5])) {

                  /*if ($value['paid_status'] == '1') {
                    $collect_status = '2';
                    $collect_status_text = '已派发';//claimed
                  }
                  else{
                    if ($value['payout_collection_type'] == 1){ //Player need to request the promo reward
                        if ($value['payout_collection_request_status'] == 1) { // player already requested
                            $collect_status_text = '审核中';//under review
                            $collect_status = '1';
                        } else { 
                            $collect_status_text = '领取';//collect
                            $collect_status = '0';
                        }
                    }else {
                        $collect_status_text = '进行中';//processing
                        $collect_status = '1';
                    }
                  }*/

                  if ($value['paid_status'] == '1') {
                    $collect_status = '2';
                    $collect_status_text = Raise::t('h5','claimed_collect_text');//claimed
                  }
                  else{
                    if ($value['payout_collection_type'] == 1){ //Player need to request the promo reward
                        if ($value['payout_collection_request_status'] == 1) { // player already requested
                            $collect_status_text = Raise::t('h5','review_collect_text');//under review
                            $collect_status = '1';
                        } else { 
                            $collect_status_text = Raise::t('h5','claim_btn_label');//collect
                            $collect_status = '0';
                        }
                    }else {

                        if ($value['payout_verify_type'] == 1) {
                            $collect_status_text = Raise::t('h5','review_collect_text');//under review
                            $collect_status = '1';
                        } else {
                            $collect_status_text = Raise::t('h5','ongoing_label');//processing
                            $collect_status = '1';
                        }
                    }
                  }
                  

              } else {

                  $promo_activity_id = $value['id'];
                  $payout_collection_type = $value['payout_collection_type'];
                  $payout_verify_type = $value['payout_verify_type'];

                  if ($value['promo_type'] == 2) {

                      $collect_status_text = Raise::t('h5','ongoing_label'); // processing - means nothing pending
                      $collect_status = '1';

                  } else {

                      if ($payout_collection_type == 0) {

                          $pending_review_count = $this->callSql("SELECT COUNT(*) FROM promo_activity_rule_applied WHERE promo_activity_list_id = '$promo_activity_id' AND status = 2 AND bonus_paid_status = 0 ","value");

                          if ($pending_review_count > 0) {
                              $collect_status_text = Raise::t('h5','review_collect_text');//under review -admin approve pending
                              $collect_status = '1';
                          } else {

                              $paid_count = $this->callSql("SELECT COUNT(*) FROM promo_activity_rule_applied WHERE promo_activity_list_id = '$promo_activity_id' AND status = 2 AND bonus_paid_status = 1 ","value");

                              if ($paid_count > 0) {
                                  $collect_status_text = Raise::t('h5','claimed_collect_text');//claimed
                                  $collect_status = '2';
                              } else {
                                  $collect_status_text = Raise::t('h5','ongoing_label'); // processing - means nothing pending
                                  $collect_status = '1';
                              }
                          }
                      } else {

                          $pending_collect_count = $this->callSql("SELECT COUNT(*) FROM promo_activity_rule_applied WHERE promo_activity_list_id = '$promo_activity_id' AND status = 2 AND bonus_collect_request_status = 0 AND bonus_paid_status = 0 ","value");

                          if ($pending_collect_count > 0) {
                              $collect_status_text = Raise::t('h5','claim_btn_label');//collect
                              $collect_status = '0';
                          } else {

                              $pending_review_count = $this->callSql("SELECT COUNT(*) FROM promo_activity_rule_applied WHERE promo_activity_list_id = '$promo_activity_id' AND status = 2 AND bonus_collect_request_status = 1 AND bonus_paid_status = 0 ","value");

                              if ($pending_review_count > 0) {
                                  $collect_status_text = Raise::t('h5','review_collect_text');//under review -admin approve pending
                                  $collect_status = '1';
                              } else {

                                  $paid_count = $this->callSql("SELECT COUNT(*) FROM promo_activity_rule_applied WHERE promo_activity_list_id = '$promo_activity_id' AND status = 2 AND bonus_paid_status = 1 ","value");

                                  if ($paid_count > 0) {
                                      $collect_status_text = Raise::t('h5','claimed_collect_text');//claimed
                                      $collect_status = '2';
                                  } else {
                                      $collect_status_text = Raise::t('h5','ongoing_label'); // processing - means nothing pending
                                      $collect_status = '1';
                                  }
                              }
                          }
                      }
                  }
              }

          }
          else {

                if ($value['promo_status'] == 0) {
                  $collect_status_text = Raise::t('h5','expired_collect_text');//Expired
                  $collect_status = '3';
                }
                  else if ($value['promo_status'] == 6) {
                  $collect_status_text = Raise::t('h5','failpromo_collect_text'); //failed by promo amount below an amount
                  $collect_status = '4';
                }

                else if ($value['promo_status'] == 3) {
                  $collect_status_text = Raise::t('h5','claimed_collect_text'); //force success
                  $collect_status = '2';
                }
                else if ($value['promo_status'] == 4) {
                  $collect_status_text = Raise::t('h5','failpromo_collect_text');//cancelled
                  $collect_status = '4';
                } else if ($value['promo_status'] == 2 && $value['paid_status'] == 0 && $value['promo_type'] == 5) {
                    if ($value['payout_collection_type'] == 1){ //Player need to request the promo reward
                        if ($value['payout_collection_request_status'] == 1) { // player already requested
                            $collect_status_text = Raise::t('h5','review_collect_text');//under review
                            $collect_status = '1';
                        } else { 
                            $collect_status_text = Raise::t('h5','claim_btn_label');//collect
                            $collect_status = '0';
                        }
                    } else {
                       /* $collect_status_text = '进行中';//processing
                        $collect_status = '1';*/
                         if ($value['payout_verify_type'] == 1) {
                            $collect_status_text = Raise::t('h5','review_collect_text');//under review
                            $collect_status = '1';
                        } else {
                            $collect_status_text = Raise::t('h5','ongoing_label');//processing
                            $collect_status = '1';
                        }
                    }
                }
                else {
                  $collect_status_text = Raise::t('h5','claimed_collect_text');//completed
                  $collect_status = '2';
                }

          }


          $promo_activity_id = $value['id'];
          if (in_array($value['promo_type'],[1,5])) {

              if((int)$value['turnover_reached'] == 0){
                $percentTurnover = 0;
              }else{
                $percentTurnover = ((float)$value['turnover_reached']/(float)$value['turnover_req'])*100;
              }

          } else {

              if (in_array($value['promo_type'],[4,6])) {
                  $percentTurnover = 100;
              } else if ($value['promo_type'] == 2) {

                  $last_bet_time = $this->callSql("SELECT last_bet_time FROM promo_activity_list WHERE id = '$promo_activity_id' ","value");

                  if ($last_bet_time > 0 && date('Y-m-d') == date('Y-m-d',$last_bet_time)) {
                      $today_reached_turnover = $value['turnover_req'];
                  } else {
                      $today_reached_turnover = 0;
                  }

                  $vip_setting_type = $this->callSql("SELECT vip_setting_type FROM promo_list WHERE id = '$promo_list_id' ","value");

                  $vip_level_id = $value['player_vip_id'] ?? 0;

                  $vip_level_id = ($vip_setting_type == 1) ? 0 : $vip_level_id;

                  $max_rule_effective_turnover = $this->callSql("SELECT MAX(min_effective_turnover) FROM promo_condition_list WHERE promo_list_id = '$promo_list_id' AND vip_level_id = '$vip_level_id' ORDER BY min_effective_turnover*1 DESC LIMIT 1","value");

                  if ($max_rule_effective_turnover > 0 && $today_reached_turnover > 0) {

                      $percentTurnover = ( $today_reached_turnover / $max_rule_effective_turnover ) * 100;
                  } else {
                      $percentTurnover = 0;
                  }

                  //change the status only if the promo is running based in percentturnover
                if ($value['promo_status'] == 1) {

                  $collect_status_text = Raise::t('h5','ongoing_label');//processing
                  $collect_status = '1';

                  //collect button status

                  if ($percentTurnover >= 100) {
                      $paid_count = $this->callSql("SELECT COUNT(*) FROM promo_activity_rule_applied WHERE promo_activity_list_id = '$promo_activity_id' AND status = 2 AND bonus_paid_status = 1 AND requirement_date = '".date('Y-m-d')."' ","value");

                      if ($paid_count > 0) {
                          $collect_status_text = Raise::t('h5','claimed_collect_text');//claimed
                          $collect_status = '2';
                      } 
                  }

                  /*if ($value['payout_collection_type'] == 1) {

                      $pending_collect_count = $this->callSql("SELECT COUNT(*) FROM promo_activity_rule_applied WHERE promo_activity_list_id = '$promo_activity_id' AND status = 2 AND bonus_collect_request_status = 0 AND bonus_paid_status = 0 ","value");

                      if ($pending_collect_count > 0) {
                          $collect_status_text = '领取';//collect
                          $collect_status = '0';
                      } 

                  }*/
                  //

                  if ($value['payout_verify_type'] == 1){
                        $pending_review_count = $this->callSql("SELECT COUNT(*) FROM promo_activity_rule_applied WHERE promo_activity_list_id = '$promo_activity_id' AND status = 2 AND bonus_paid_status = 0 ","value");
                        if($pending_review_count > 0) {
                            $collect_status_text = Raise::t('h5','review_collect_text');//under review
                            $collect_status = '1';
                        }
                  }
                  if ($value['payout_collection_type'] == 1) {

                      $pending_collect_count = $this->callSql("SELECT COUNT(*) FROM promo_activity_rule_applied WHERE promo_activity_list_id = '$promo_activity_id' AND status = 2 AND bonus_collect_request_status = 0 AND bonus_paid_status = 0 ","value");

                      if ($pending_collect_count > 0) {
                          $collect_status_text = Raise::t('h5','claim_btn_label');//collect
                          $collect_status = '0';
                      } 

                  }
              }
              }  
            }

          
            $dataList[$key]['promo_status_text'] = $collect_status_text;
            $dataList[$key]['collect_status'] = $collect_status;
            // return $dataList;
        }
        return $dataList;
    }
    

    /**
     * get condition list data
     *
     * @param int $id
     * @return boolean $status
     */
    public function getConditionlistData($request)
    {
        $promoId = $request['promoId'];
        $wherQuery = '';
        if(!empty($promoId)){
            $wherQuery .= ' WHERE pl.promo_list_id = p.id and  p.id = '.$promoId.'  AND pl.language_code = "'.$language.'"  ';
        }
        $dataArray          = [];
        $query  = 'SELECT ';
        $query .= '     pl.promo_list_id,
                        pl.title,
                        pl.description_app,
                        p.promo_start_time,
                        p.promo_end_time,
                        p.promo_sub_type,
                        p.promo_type,
                        p.max_apply_count
                        ';
        $query .= ' FROM promo_list as p,promo_list_display_language as pl  '.$wherQuery.' ';
        $this->query($query);
        $dataList = $this->resultset();
        return $dataList;
    }


    

    /**
     * get all promo list for after login condition
     *
     * @param int $id
     * @return boolean $status
     */
    // public function getPromoApplyList($post)
    // {   
    //     $result = [];
    //     $promo_list = [];
    //     $language   = Raise::$lang;
    //     $playerId   = $post['player_id'];
    //     $device     = isset($post['device'])?$post['device']:'1';
    //     $affiliate  = '';
    //     $param['player_id']      = $playerId;
    //     // $microArr [] = [
    //     //               'serviceName' => MICRO_SERVICES['microPlayer'],
    //     //               'command'     => 'PlayerUserInfo/getAffiliate',
    //     //               'param'       => $param,
    //     //           ];

    //     // if (!empty($microArr)) {
    //     //     $result_arr = Raise::callApi($microArr);
    //     //     $result = $result_arr[0];
    //     //     if($result['success']) {
    //     //         $affiliate = $result['data'];
    //     //     } 
    //     // }           
    //     $affiliate_code = !empty($affiliate)?$affiliate:'';
    //     $page           = isset($post['page'])?$post['page']:'';
    //     $perPage        = isset($post['perPage'])?$post['perPage']:'';
    //     $page           = ((int)$page > 0 )? $page : 1;
    //     $perPage        = ((int)$perPage > 0 )? $perPage :5;
    //     $pageStart      = ($page - 1) * $perPage;
    //     $type_array['en']      = array('1'=>'Register','2'=>'Turnover','3'=>'Rescue','4'=>'Red Packet','5'=>'Deposit','6'=>'Custom');
    //     $type_array['zh_hans'] = array('1'=>'Register','3'=>'Turnover','3'=>'Rescue','4'=>'Red Packet','5'=>'Deposit','6'=>'Custom');

    //     $whereQuery     = ' AND pl.status = 1 AND pl.apply_type = 1  ';
    //     $whereQuery    .= ' AND p.language_code = "'.$language.'"  ';

    //     $whereQuery    .= ' AND  ( CASE WHEN pl.target_type = 3 THEN FIND_IN_SET(\''.$playerId.'\', pl.target_list) ';
    //     $whereQuery    .= ' WHEN  pl.target_type = 2 THEN FIND_IN_SET(\''.$affiliate_code.'\', pl.target_list) ';
    //     $whereQuery    .= ' ELSE pl.target_type=1 END )  ';

    //     if(!empty($device)) {
    //         $whereQuery .= ' AND FIND_IN_SET('.$device.',pl.device) ';
    //     }
    //     $time = time();
    //     $sqlCount = 'SELECT COUNT(*) as count
    //         FROM promo_list as pl
    //         JOIN promo_list_display_language as p ON(p.promo_list_id = pl.id) 
    //         WHERE ( CASE 
    //                     WHEN pl.promo_end_time != 0 THEN   pl.promo_start_time <= '.$time.' AND (pl.promo_end_time + 86400) >= '.$time.' 
    //                     WHEN pl.promo_end_time = 0 THEN  pl.promo_start_time <= '.$time.'
    //                     ELSE 1 
    //                 END ) '.$whereQuery.' ';
    //     $this->query($sqlCount);
    //     $totalCount = $this->getValue();
    //     $query  = 'SELECT ';
    //     $query .= '     pl.id,
    //                     pl.promo_name,
    //                     pl.promo_start_time,
    //                     pl.promo_end_time,
    //                     pl.promo_type,
    //                     pl.promo_sub_type,
    //                     pl.promo_name,
    //                     pl.game_types,
    //                     pl.game_vendors,
    //                     pl.amount,
    //                     p.promo_list_id,
    //                     p.title,
    //                     p.language_code,
    //                     p.description_h5,
    //                     p.description_app,
    //                     p.description_web,
    //                     p.banner_1,
    //                     p.banner_2,
    //                     p.icon
    //                     ';
    //     $query .= ' FROM promo_list as pl JOIN promo_list_display_language as p ON(p.promo_list_id = pl.id) WHERE 
    //                 ( CASE 
    //                     WHEN pl.promo_end_time != 0 THEN   pl.promo_start_time <= '.$time.' AND (pl.promo_end_time + 86400) >= '.$time.'  
    //                     WHEN pl.promo_end_time = 0 THEN  pl.promo_start_time <= '.$time.' 
    //                     ELSE 1 
    //                 END ) '.$whereQuery.' ORDER BY pl.created_at DESC LIMIT '.$pageStart.','.$perPage.' ';
    //     $this->query($query);
    //     $dataList = $this->resultset();
    //     if(!empty($dataList)){
    //         foreach ($dataList as $index => $value) {
    //              if($device == '3'){
    //                 $description = $value['description_app'];
    //             }
    //             else if($device == '2'){
    //                 $description = $value['description_h5'];
    //             }
    //             else{
    //                 $description = $value['description_web'];
    //             }
    //             $validate_user  = $this->checkValidatePromo($value['id']);
    //             $promo_name_arr = json_decode($value['promo_name'],true);
    //             $promo_list[]   = array(
    //                 'promo_list_id'     => $value['id'],
    //                 'promo_name'        => isset($promo_name_arr[$language])?$promo_name_arr[$language]:'',
    //                 'promo_type'        => isset($type_array[$language][$value['promo_type']])?$type_array[$language][$value['promo_type']]:'',
    //                 'promo_start_time'  => !empty($value['promo_start_time'])?date('d-m-Y H:i:s',$value['promo_start_time']):'-',
    //                 'promo_end_time'    => !empty($value['promo_end_time'])?date('d-m-Y H:i:s',$value['promo_end_time']):'-',
    //                 'promo_type'        => $value['promo_type'],
    //                 'game_types'        => $value['game_types'],
    //                 'title'             => $value['title'],
    //                 'description'       => $description,
    //                 'amount'            => !empty($value['amount'])?number_format($value['amount']):'0',
    //                 'banner_1'          => $value['banner_1'],
    //                 'banner_2'          => $value['banner_2'],
    //                 'icon'              => $value['icon'],
    //                 'valid_promo_stat'  => ($validate_user == false)?0:1
    //             );
    //         }
    //     }
    //     $result = array('promo_list'=>$promo_list,'totalCount'=>$totalCount,'perPage'=>$perPage);

    //     return $result;
    // }

     public function checkValidatePromo($promoId)
    {  
        return true;
    } 

    /**
     * Update promo details by id
     *
     * @param array update columns and value $ip
     * @param int $id
     * @return boolean $status
     */
    public function updatePromo($ip, $id)
    {
        if (empty($ip)) {
            return false;
        }

        $up_columns = array();
        array_walk($ip, function ($val, $col) use (&$up_columns) {
            $up_columns[] = $col.'=:'.$col;
        });

        $sql = 'UPDATE '.$this->tableName.' SET '.implode(',', $up_columns).' WHERE id=:id';
        $this->query($sql);

        array_walk($ip, function ($val, $col) {
            $this->bind($col, $val);
        });

        $this->bind('id', $id);

        return $this->execute();
    }

    /**
     * Search by promo
     *
     * @param string $name
     * @param int $ids
     * @param string $lan [en, zh_hans]
     * @return array 
     */
    public function getConflictPromos($name, $ids = '', $lan = 'en')
    {
        $where_str_array = array();

        $where_str_array[] = 'promo.id=lan.promo_list_id';

        if (!empty($name)) {
            $where_str_array[] = 'lan.title LIKE \'%'.$name.'%\'';
        }

        if (!empty($ids)) {
            $where_str_array[] = 'promo.id IN ('.$ids.')';
        }

        if (!empty($lan)) {
            $where_str_array[] = 'lan.language_code=\''.$lan.'\'';
        }

        $where_str = ' 1 ';
        if (!empty($where_str_array)) {
            $where_str = implode(' AND ', $where_str_array);
        }

        $sql = 'SELECT promo.id, lan.title FROM promo_list AS promo, promo_list_display_language AS lan WHERE '.$where_str;

        $this->query($sql);

        return $this->resultset();
    }

    public function getAllAutoApplyPromo($type = array())
    {
        $time = time();

        $sql = 'SELECT * FROM '.$this->tableName.' WHERE promo_type IN ('.implode(',', $type).') AND apply_type=0 AND status=1 AND delete_status!=1 AND promo_start_time<='.$time.' AND (promo_end_time=0 OR promo_end_time>='.$time.') ORDER BY id ASC';
        
        $this->query($sql);

        return $this->resultset();
    }

    public function checkValidMissionPromoExists($sub_type, $from, $promo_id)
    {
        return $this->callSql('SELECT COUNT(*) FROM promo_list WHERE promo_type=8 AND promo_sub_type=\''.$sub_type.'\' AND status=1 AND (promo_end_time=0 OR promo_end_time>=\''.$from.'\') '.(!empty($promo_id)?' AND id!=\''.$promo_id.'\'':'').'', 'value');
    }  

    public function getPromoByType($type)
    {
        $time = time();

        $sql = 'SELECT id, promo_type, promo_sub_type, verify_details, target_type, target_list, member_level_list, payout_period_type, vip_setting_type, game_types, game_vendors, game_vendors_games, sign_days, sign_recover_days_limit, sign_recover_deposit_count, sign_recover_deposit_amount, sign_recover_turnover, sign_recover_bet_count, sign_recover_bet_amount, sign_days_bonus, sign_days_turnover_times, finish_turnover_times FROM '.$this->tableName.' ';

        $sql .= ' WHERE promo_start_time<='.$time.' AND (promo_end_time=0 OR promo_end_time>='.$time.') AND promo_type='.$type.'';

        return $this->callSql($sql, 'rows');
    }

    //angapo functions
    public function getActiveAngPao($promo_list_id,$filter=[])
    {
        $cur_time = time();

        $where = " WHERE pl.promo_type = 4 AND pl.status = 1 AND pl.delete_status = 0 AND ((pl.promo_end_time > 0 AND pl.promo_end_time >= '".$cur_time."') OR pl.promo_end_time = 0 ) AND pl.promo_start_time <= '".$cur_time."' ";

        $type = "rows";
        if ($promo_list_id > 0) {
            $where .= " AND pl.id = '$promo_list_id' ";
            $type = "row";
        } 

        foreach ($filter as $key => $value) {
            $where .= " AND pl.$key ".$value." ";
        }

        $sql = "SELECT
                    pl.id,
                    pl.promo_name as promo_name_schn,
                    pl.promo_end_time,
                    pl.angpao_session,
                    pl.target_type,
                    pl.target_list,
                    pl.member_level_list,
                    pl.verify_details,
                    pl.conflict_promos,
                    pl.vip_setting_type,
                    pl.min_bet_count as angpao_bet_times,
                    pl.min_bet_amount as angpao_bet_amount,
                    pl.min_deposit_count as angpao_deposit_times,
                    pl.min_deposit_amount as angpao_deposit_amount,
                    pl.unique_ip_apply_count as angpao_ip_apply_times,
                    pl.unique_appid_apply_count as angpao_app_udid_apply_times,
                    pl.unique_name_apply_count as angpao_name_apply_times,
                    pl.payout_timing_type as angpao_payout_timing_type,
                    pl.payout_timing_end_time as angpao_payout_timing_end,
                    pl.payout_timing_start_time as angpao_payout_timing_start,
                    pl.payout_timing_hours,
                    pl.payout_timing_mins,
                    pl.payout_timing_seconds,
                    (SELECT l.description_app FROM promo_list_display_language l WHERE l.promo_list_id = pl.id AND l.language_code = 'zh_hans' LIMIT 1) as promo_noti_body
                    FROM promo_list pl $where ";

        $result = $this->callSql($sql ,$type);

        return $result;

    }

    //angapo functions
    public function getActiveAngPaoOld($promo_list_id,$filter=[])
    {
        $cur_time = time();

        $where = " WHERE pl.promo_type = 4 AND pl.status = 1 AND pl.delete_status = 0 AND ((pl.promo_end_time > 0 AND pl.promo_end_time >= '".$cur_time."') OR pl.promo_end_time = 0 ) AND pl.promo_start_time <= '".$cur_time."' ";

        if ($promo_list_id > 0) {
            $where .= " AND pl.id = '$promo_list_id' ";
        } 

        foreach ($filter as $key => $value) {
            $where .= " AND pl.$key ".$value." ";
        }


        $promoQry = $this->callSql("SELECT
                                    pl.id,
                                    pl.promo_name as promo_name_schn,
                                    pl.promo_end_time,
                                    pl.angpao_session,
                                    pl.target_type,
                                    pl.target_list,
                                    pl.member_level_list,
                                    pl.verify_details,
                                    pl.conflict_promos,
                                    pl.min_bet_count as angpao_bet_times,
                                    pl.min_bet_amount as angpao_bet_amount,
                                    pl.min_deposit_count as angpao_deposit_times,
                                    pl.min_deposit_amount as angpao_deposit_amount,
                                    pl.unique_ip_apply_count as angpao_ip_apply_times,
                                    pl.unique_appid_apply_count as angpao_app_udid_apply_times,
                                    cl.bonus_range_setting as angpao_json,
                                    pl.payout_timing_type as angpao_payout_timing_type,
                                    pl.payout_timing_end_time as angpao_payout_timing_end,
                                    pl.payout_timing_start_time as angpao_payout_timing_start,
                                    pl.payout_timing_hours,
                                    pl.payout_timing_mins,
                                    pl.payout_timing_seconds,
                                    l.description_app as promo_noti_body
                                    FROM promo_list pl JOIN promo_condition_list cl ON (pl.id = cl.promo_list_id)
                                    JOIN promo_list_display_language l ON(pl.id = l.promo_list_id) AND language_code = 'zh_hans'
                                    $where " ,"rows");

        return $promoQry;

    }

    public function getAllUser($page,$perPage=1000)
    {   

        $query = "SELECT player_id,device_os FROM player_device_list WHERE device_id != '' ORDER BY updated_at DESC LIMIT $pageStart,$perPage ";

        $player_user = $this->callSql($query, "rows");

        return $player_user;

    }

    public function getUserTotalCount() {

        $query = "SELECT COUNT(id) FROM player_device_list WHERE device_id != '' ";

        $totalCount = $this->callSql($query, "value");

        return $totalCount;
    }

    public function getAllUserWithDevice()
    {   
        $query = "SELECT player_id, device_id
                    FROM player_device_list
                    WHERE id IN
                        (SELECT id
                            FROM player_device_list a
                            RIGHT JOIN (SELECT MAX(updated_at) AS latest, player_id
                                FROM player_device_list
                                GROUP BY player_id) b
                            ON a.player_id = b.player_id AND a.updated_at = b.latest
                        )
                    GROUP BY player_id;";

        $player_user = $this->callSql($query, "rows");

        return $player_user;

    }


    public function getPromoAllDetails($promo_list_id){

        $result = $this->callSql("SELECT * FROM promo_list WHERE id = '$promo_list_id' LIMIT 1","row");

        $result = $result ?? [];

        return $result;

    }

    public function getPromoActiveDetails($req){
        $promo_list_id = $req['promoId'];
        $player_id = $req['player_id'];
        $time = time();
        $whereQry = " AND promo_start_time<=".$time." AND (promo_end_time=0 OR promo_end_time>=".$time.") AND status=1 ";
        if(!empty($player_id)){
            $whereQry .= " AND apply_type = 1 ";
        }
        $result = $this->callSql("SELECT * FROM promo_list WHERE id = '$promo_list_id' and delete_status = 0 $whereQry LIMIT 1 ","row");

        $result = $result ?? [];

        return $result;

    }

    public function getAllRedPacketPromos(){

        $result = $this->callSql("SELECT * FROM $this->tableName WHERE promo_type = '4' ","rows");

        $result = $result ?? [];

        return $result;

    }

    public function updateRedpacket($promo_list_id,$data){

        $update = [];
        foreach($data as $key => $value){
            $update[] = " $key = '".$value."' ";
        }

        $update = array_filter($update);

        if(empty($update)){

            return false;
        }

        $sql = "UPDATE promo_list SET ". implode(',',$update) ."
                    WHERE id = '$promo_list_id' ";

        $this->query($sql);

        $this->execute();

        return true;
    }


    public function checkPlayerMatch($validate_arr, $players, $stime, $etime, $today, $promo_id){

        if (empty($players)) {
            return [];
        }

        $cond_bet_count     = $validate_arr['bet_times'] ?? 0;
        $cond_bet_amount    = $validate_arr['bet_amount'] ?? 0;
        $cond_dep_count     = $validate_arr['deposit_times'] ?? 0;
        $cond_dep_amount    = $validate_arr['deposit_amount'] ?? 0;

        if (empty($cond_bet_count) && empty($cond_bet_amount) && empty($cond_dep_count) && empty($cond_dep_amount)) {
            return $players;
        }
        
        $winloss = $this->getPlayersWinlossDeposit($players);

        if (empty($winloss)) {
            return [];
        }

        $new_players = [];

        foreach ($players as $player_id) { 

            $details = $winloss[$player_id] ?? [];

            if (empty($details)) {
                continue;
            }

            $user_bet_count     = $details['total_bet_count'] ?? 0;
            $user_bet_amount    = $details['total_bet_amount'] ?? 0;
            $user_dep_count     = $details['deposit_count'] ?? 0;
            $user_dep_amount    = $details['deposit_amount'] ?? 0;

            if ($cond_bet_count > $user_bet_count) {
                continue;
            }

            if ($cond_bet_amount > $user_bet_amount) {
                continue;
            }

            if ($cond_dep_count > $user_dep_count) {
                continue;
            }

            if ($cond_dep_amount > $user_dep_amount) {
                continue;
            }

            $new_players[] = $player_id;
        }

        return $new_players;
    }

    public function getPlayerCanJoin($promo_info, $players){ 

        if (empty($players)) {
            return [];
        }

        $target_type        = $promo_info['target_type'] ?? 1;
        $target_list        = $promo_info['target_list'] ?? "";
        $member_level_list     = $promo_info['member_level_list'] ?? "";
        $verify_details     = $promo_info['verify_details'] ?? "";
        $conflict_promos     = $promo_info['conflict_promos'] ?? "";
        $vip_setting_list     = $promo_info['vip_setting_list'] ?? [];

        $target_list = explode(",",$target_list);
        $member_level_list = explode(",",$member_level_list);
        $verify_details = explode(",",$verify_details);
        $conflict_promos = explode(",",$conflict_promos);

        if (in_array($target_type,[1,3]) && empty($member_level_list) && empty($verify_details) && empty($conflict_promos)) {
            return $players;
        }

        $user_array = getModel('PromoActivityList')->getUsers($players); 

        $count = !empty($user_array['recordsFiltered'])?$user_array['recordsFiltered']:0;
        $users = !empty($user_array['data'])?$user_array['data']:array();

        if (empty($users)) {
            return [];
        }

        $new_players = [];

        foreach($users as $user) {

            /*check target type if 2 check affiliate id is valid*/
            if ($target_type == 2 && !in_array($user['affiliate_id'], $target_list)) {
                continue;
            } 

            /*check member level is elegible for this promo*/
            if (!empty($member_level_list) && !in_array($user['player_group_id'],$member_level_list)) {
                continue;
            } 

            if (!empty($verify_details)) {
                /*check mobile verification status*/
                if (in_array(1, $verify_details) && $user['mobile_verification_status'] != 1) {
                    continue;
                }

                /*check withdraw pin*/
                if (in_array(2, $verify_details) && empty($user['is__security_pin'])) {
                    continue;
                }

                /*check player bank*/
                if (in_array(3, $verify_details) && empty($user['is__bank'])) {
                    continue;
                }
            }

            /*check vip level condition*/
            $vip_id = 0;
            if ($promo_info['vip_setting_type'] != 1) {
                $vip_id = $user['player_vip_id'] ?? '';
            }

            if(!in_array($vip_id, $vip_setting_list)) {
                continue;
            }

            $new_players[] = $user['id'];
        }

        return $new_players;
    }

    public function getPlayersWinlossDeposit($players)
    {
        if (empty($players)) {
            return [];
        }
    
        $i = 0;
        $result = $request = array();

        $request[0]['serviceName'] = MICRO_SERVICES['report'];
        $request[0]['command'] = 'gamePlayHistory/promoPlayerDetails';
        $request[0]['param']['player_id'] = $players;
        $request[0]['param']['from_date'] = "0000:00:00";
        $request[0]['param']['to_date'] = date('Y-m-d');
        
        $res = Raise::callApi($request);

        $bet_info = $res[0]['data']['data'] ?? [];
        
        foreach ($bet_info as $user_id => $bet) {
            $result[$user_id] = $bet;
        }

        return $result;
    }


    public function saveRedpacketGrabHistory($player_id,$promo_id,$session,$amount,$device_ip,$device_id,$time){
        $insert_query = "INSERT INTO `promo_angpao_grab_history`
                        SET `player_id` = '$player_id',
                            `promo_id` = '$promo_id',
                            `session` = '$session',
                            `amount` = '$amount',
                            `ip_addr` = '$device_ip',
                            `device_id` = '$device_id',
                            `create_time` = '$time'";

        $this->query($insert_query);

        $this->execute();

        return true;
    }

    public function updateRedpacketGrabHistory($ids,$data){

        $update = [];
        foreach($data as $key => $value){
            $update[] = " $key = '".$value."' ";
        }

        $update = array_filter($update);

        if(empty($update)){

            return false;
        }

        $query = "UPDATE promo_angpao_grab_history SET ". implode(',',$update) ." WHERE id IN ($ids)";

        $this->query($query);

        $this->execute();

        return true;
    }

    public function getRedpacketGrabHistory($filter){

        $where = " WHERE 1 ";
        foreach($filter as $key => $value){
            $where .= " AND $key = '".$value."' ";
        }

        $result = $this->callSql("SELECT * FROM `promo_angpao_grab_history` $where ","rows");

        if (empty($result)) {
            $result = [];
        }

        return $result;

    }

    public function getPromoPayoutInfo($ids)
    {
        $where_str = '';
        if (is_array($ids)) {
            $where_str = ' id IN ('.implode(',', $ids).') ';
        } else {
            $where_str = ' id=\''.$ids.'\' ';
        }

        $sql = 'SELECT id, promo_name, promo_sub_type, payout_collection_type, payout_verify_type, game_vendors, display_type, display_type_url, display_type_new_tab FROM '.$this->tableName.' WHERE '.$where_str;

        return $this->callSql($sql, 'rows');
    }
    
    public function getAllGoingPromo()
    {
        $time = time();

        $sql = 'SELECT id, promo_type, promo_sub_type, vip_setting_type FROM '.$this->tableName.' ';

        $sql .= ' WHERE promo_start_time<='.$time.' AND (promo_end_time=0 OR promo_end_time>='.$time.') AND status=1 ';

        return $this->callSql($sql, 'rows');
    }

    public function getPromoIdsByAdmin($promo_type, $promo_sub_type, $admin_id, $promo_id)
    {
        $sql = 'SELECT id, promo_name FROM '.$this->tableName.' WHERE promo_type=\''.$promo_type.'\' AND promo_sub_type=\''.$promo_sub_type.'\' AND created_by=\''.$admin_id.'\' ';

        if (!empty($promo_id)) {
            $sql .= ' AND id!=\''.$promo_id.'\'';
        }

        return $this->callSql($sql, 'rows');
    }

    public function getLastPromoRequestTime($admin_id)
    {
        $sql = 'SELECT created_at FROM '.$this->tableName.' WHERE created_by=\''.$admin_id.'\' ORDER BY id DESC LIMIT 0, 1';

        return $this->callSql($sql, 'value');
    }
    
     public function getNameById($id)
    {
        $language   = Raise::$lang;
        $sql = 'SELECT promo_name FROM '.$this->tableName.'  WHERE id =  '.$id.'';

        $nameJson = $this->callSql($sql, 'value');
        $promo_name_arr = json_decode($nameJson,true);
        $promoName = $promo_name_arr[$language] ?? $id;
        return $promoName;
    }

    public function getExpiredPromos($time = 0,$promo_types="")
    {

        if (empty($time)) {
            $time = time();
        }

        $where = "";
        if (!empty($promo_types)) {
            $where .= " AND promo_type IN ($promo_types) ";
        }

        $promos = $this->callSql("SELECT * FROM promo_list WHERE promo_end_time > 0 AND promo_end_time < $time $where ","rows");

        return $promos;
    }
    
}
