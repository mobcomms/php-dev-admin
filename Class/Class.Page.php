<?php
class __paging__ {

	public $ps, $pb, $np;

	public function init($total) {
		$this->ps = (empty($this->ps)) ? 20 : $this->ps;	// 한페이지내 리스트 갯수
		$this->pb = (empty($this->pb)) ? 10 : $this->pb;		// 페이지 블록
		$this->np = (empty($this->np)) ? 1 : $this->np;		// 현재 페이지
		$PG = new stdClass();
		$PG->size = $this->ps;
		$PG->now = $this->np;
		$PG->pb = $this->pb;
		$PG->all = $total;
		$PG->block = ceil($PG->all / $this->ps);
		$PG->nb = ceil($this->np / $this->pb);
		$PG->tmp = ($PG->nb * $this->pb) - ($this->pb-1);
		$PG->start = ($PG->tmp <= 1) ? 1 : $PG->tmp;
		$PG->tmp = ($PG->nb * $this->pb);
		$PG->end = ($PG->all <= $PG->tmp) ? $PG->all : $PG->tmp;
		$PG->first = ($PG->now - 1) * $this->ps;
		$PG->first_num = $PG->all - ($PG->now - 1) * $PG->size;
		return $PG;
	}

	public function paging($object, $ret = false) {
		if($object->start >= $object->now) { $start['class'] = 'disabled'; $start['link'] = 'javascript:;'; } else { $start['class'] = ''; $start['link'] = '?np='.$object->start; }
		if($object->block <= $object->now) { $end['class'] = 'disabled'; $end['link'] = 'javascript:;'; } else { $end['class'] = ''; $end['link'] = '?np='.$object->block; }
		$return = '
	<div style="text-align:center;">
		<ul class="pagination">
			<li class="'.$start['class'].'"><a href="'.$start['link'].'"><span class="glyphicon ti-angle-double-left"></span></a></li>
		';
		for($i = $object->start ; $i <= $object->tmp ; $i++) { $return .= '<li class="'.(($i == $object->now)?'active':'').'"><a href="?np='.$i.'">'.$i.'</a></li>'; }
		$return .= '
			<li class="'.$end['class'].'"><a href="'.$end['link'].'"><span class="glyphicon ti-angle-double-right">></span></a></li>
		</ul>
	</div>
		';
		if($ret == false) { echo $return; } else { return $return; }
	}






	public function paging_new($object, $param, $ret = false) {

		if($object->now == '1') {
			$start['class'] = 'disabled';
			$start['link'] = 'javascript:;';
		} else {
			$start['class'] = '';
			$start['link'] = '?np=1';
		}

		if($object->block <= $object->now) {
			$end['class'] = 'disabled';
			$end['link'] = 'javascript:;';
		} else {
			$end['class'] = '';
			$end['link'] = '?np='.$object->block;
		}

		$return = '
		<div class="m_pager">
		<ul class="pagination nomargin">
			<li class="'.$start['class'].'">
				<a href="'.$start['link'].$param.'">
					<span class="glyphicon ti-angle-double-left"><<</span>
				</a>
			</li>
		';
		$prev=($object->now <= $object->pb)?"disabled":"";
		$prevLink=($object->now <= $object->pb)?'<a href="javascript:;">':'<a href="?np='.($object->tmp-($object->pb)).$param.'">';
		$return .= '
			<li class="'.$prev.'">
				'.$prevLink.'
					<i class="fa fa-angle-left"></i>
				</a>
			</li>
		';
		for($i = $object->start ; $i <= $object->tmp && $i<=$object->block ; $i++) {
			if($i == $object->now) $return .= '<li class="active"><a href="javascript:;">'.$i.'</a></li>';
			else $return .= '<li><a href="?np='.$i.$param.'">'.$i.'</a></li>';
		}

		$nexLink=($object->block <= $object->now)?'<a href="javascript:;">':'<a href="?np='.($object->tmp+1).$param.'">';
		$return .= '
			<li class="'.$end['class'].'">
				'.$nexLink.'
					<i class="fa fa-angle-right"></i>
				</a>
			</li>
		';
		$return .= '
			<li class="'.$end['class'].'">
				<a href="'.$end['link'].$param.'">
					<span class="glyphicon ti-angle-double-right">>></span>
				</a>
			</li>
		</ul>
		</div>
		';
		if($ret == false) { echo $return; } else { return $return; }
	}

}

if(!isset($paging)) {
	$paging = new __paging__;
}
