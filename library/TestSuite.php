<?php
/**
 * 单元测试底层
 *
 * 所有需要进行测试的类的父类
 *
 * 调用example:
 *     class xxx extends TestSuite{}
 *
 * @author zhiyuan <zhiyuan12@staff.sina.com.cn>
 */
class TestSuite {
	/**
	 * 单例测试运行接口
	 */
	public function run($displayer) {
		$this->_fail_cases = array ();
		$this->_pass_cases = array ();
		$class_name = get_called_class ();
		$this->beginTest ();
		foreach ( get_class_methods ( $class_name ) as $method ) {
			if (start_with ( $method, "test" )) {
				$displayer->normal ( "Running $class_name.$method" );
				try {
					$this->SetUp ();
					$this->$method ();
					$this->CleanUp ();
				} catch ( Exception $e ) {
					$displayer->fail ( "[FAILED] test case $class_name.$method failed" );
					$displayer->fail ( nl2br ( htmlspecialchars ( $e->getMessage () ) ) );
					$this->_fail_cases [] = "$class_name.$method";
					continue;
				}
				$displayer->pass ( "[PASSED] test case $class_name.$method passed" );
				$this->_pass_cases [] = "$class_name.$method";
			}
		}
		$this->endTest ();
		return count ( $this->_fail_cases ) == 0;
	}
	/**
	 * 单例测试方法前调用，一般覆盖使用
	 */
	public function setUp() {
	}
	/**
	 * 单例测试方法后调用，一般覆盖使用
	 */
	public function cleanUp() {
	}
	/**
	 * 单例测试开始前调用，一般覆盖使用
	 */
	public function beginTest() {
	}
	/**
	 * 单例测试结束后调用，一般覆盖使用
	 */
	public function endTest() {
	}
	/**
	 * 获取测试失败的例子
	 */
	public function failCases() {
		return $this->_fail_cases;
	}
	/**
	 * 获取通过测试的例子
	 */
	public function passCases() {
		return $this->_pass_cases;
	}
	/**
	 * 断言match
	 *
	 * @param string $expect期望值的preg_match正则表达式        	
	 * @param string $real待测试值        	
	 * @param string $ext_msg=null        	
	 */
	protected function assertMatch($expect, $real, $ext_msg = null) {
		if (preg_match ( $expect, $real, $tmp ) === 0) {
			$msg = 'assertMatch Failed :Expect regular [' . $expect . '] which really string is [' . $real . '], not match.';
			if ($ext_msg != null) {
				$msg .= "With Ext:" . $ext_msg;
			}
			$this->_throwExcption ( $msg );
		}
	}
	/**
	 * 断言数值或数值字符串
	 *
	 * @param string $expect期望值的preg_match正则表达式        	
	 * @param string $real待测试值        	
	 * @param string $ext_msg=null        	
	 */
	protected function assertNum($real, $ext_msg = null) {
		if (! is_numeric ( $real )) {
			$msg = 'assertMatch Failed :Expect numeric which really is [' . $real . '], is not num.';
			if ($ext_msg != null) {
				$msg .= "With Ext:" . $ext_msg;
			}
			$this->_throwExcption ( $msg );
		}
	}
	/**
	 * 断言equal
	 *
	 * @param mix $expect期望值        	
	 * @param mix $real待测试值        	
	 * @param string $ext_msg=null        	
	 */
	protected function assertEq($expect, $real, $ext_msg = null) {
		if ($expect !== $real) {
			$msg = 'assertEq Failed :Expect [' . $this->_var2str ( $expect ) . '] which really is [' . $this->_var2str ( $real ) . '].';
			if ($ext_msg != null) {
				$msg .= "With Ext:" . $ext_msg;
			}
			$this->_throwExcption ( $msg );
		}
	}
	/**
	 * 断言not equal
	 *
	 * @param mix $expect期望值        	
	 * @param mix $real待测试值        	
	 * @param string $ext_msg=null        	
	 */
	protected function assertNe($expect, $real, $ext_msg = null) {
		if ($expect === $real) {
			$msg = 'assertNe Failed :Expect [' . $this->_var2str ( $expect ) . '] not equal to [$' . $this->_var2str ( $real ) . '], but they do equal.';
			if ($ext_msg != null) {
				$msg .= "With Ext:" . $ext_msg;
			}
			$this->_throwExcption ( $msg );
		}
	}
	/**
	 * 断言true
	 *
	 * @param bool $val        	
	 * @param string $ext_msg=null        	
	 */
	protected function assertTrue($val, $ext_msg = null) {
		if ($val !== true) {
			$msg = 'assertTrue Failed : [' . $val . '] is not true';
			if ($ext_msg != null) {
				$msg .= "With Ext:" . $ext_msg;
			}
			$this->_throwExcption ( $msg );
		}
	}
	/**
	 * 断言false
	 *
	 * @param bool $val        	
	 * @param string $ext_msg=null        	
	 */
	protected function assertFalse($val, $ext_msg = null) {
		if ($val !== false) {
			$msg = 'assertFalse Failed :[' . $val . '] is true';
			if ($ext_msg != null) {
				$msg .= "With Ext:" . $ext_msg;
			}
			$this->_throwExcption ( $msg );
		}
	}
	private function _var2str($var) {
		return json_encode ( $var );
	}
	/**
	 * 抛出异常
	 */
	private function _throwExcption($msg = null) {
		throw new Exception ( $msg );
	}
	private $_fail_cases;
	private $_pass_cases;
}