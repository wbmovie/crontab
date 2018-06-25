<?php
/**
* 发号器
* f(n)=f(n-1)+rand(length); //逐渐递增保障唯一性
*/
class Generator{
	const BASE_X = 36;
	const LENGTH = 3;		//递增步长
	const NUMBER_MAX = 1679616;             //4位邀请码的最大边界值
	const LAST_NUMBER_KEY = 'King_GeneratorModel:LAST_NUMBER_KEY:';

	/**
	* 生成邀请码
	*
	*/
	public static function createCode($scene_id){
		if(!is_numeric($scene_id)){
        	return false;
        }
        $rand_number =  self::_getRandNumber();
        $last_number = self::_incrLastNumber($scene_id, $rand_number);
        if($last_number >= self::NUMBER_MAX){
        	return false;
        }
        $basex_obj = new Comm_BaseX(self::BASE_X);
        $str = $basex_obj->encode($last_number);
        $str = sprintf("%'04s", $str);
        return $str;
	}
	/**
	* 编码
	*/
	public static function encode($number){
		if(!is_numeric($number)){
			return false;
		}
		$basex_obj = new Comm_BaseX(self::BASE_X);
        $str = $basex_obj->encode($number);
        $str = sprintf("%'04s", $str);
        return $str;
	}
	/**
	* 解码
	*/
	public static function decode($str){
		if(empty($str)){
			return false;
		}
		$basex_obj = new Comm_BaseX(self::BASE_X);
        $number = $basex_obj->decode($str);
        return $number;
	}
	/**
     * _getRandNumber 
     * 获取一个一万以内的随机数
     * @param int $max 
     * @access private
     * @return void
     */
    private static function _getRandNumber($max = self::LENGTH) {
        $rand = mt_rand(1, $max);
        return $rand ? $rand : 1;
    }
    /**
    * 自增场次的最后战队邀请码号,返回最后的邀请码号
    * 使用incr 防止高并发数据不一致问题
    *
    */
    private static function _incrLastNumber( $scene_id, $num = self::LENGTH) {
        if(!is_numeric($scene_id)){
        	return false;
        }
        $redis_key = self::_getLastNumberKey();
        $redis = Database_Redis::of('default');
        $last_number = $redis->hincrby($redis_key, $scene_id, $num);
        return $last_number;
    }

    public static function getLastNumber( $scene_id ) {
        if(!is_numeric($scene_id)){
        	return false;
        }
        $redis_key = self::_getLastNumberKey();
        $redis = Database_Redis::of('default');
        $last_number = $redis->hget($redis_key, $scene_id);
        return $last_number;
    }

    private static function _getLastNumberKey(){
    	return self::LAST_NUMBER_KEY;
    }
}
