<?php
/**
 * Sample model class for implements FacebookGraphApiBehavior
 */

class  Facebook extends AppModel{

	public $useTable = false;
	public $actsAs = array('FacebookGraphApiBehavior.FacebookGraphApi');

}
