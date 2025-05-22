<?php
/*************************************************************
*
*	SMS 전송 Class
*
*************************************************************/

class __SMS__ {

	private $send_name;

	public function __construct(){
		parent::__construct();
		$this->send_name = 'cashkeyboard.co.kr';
	}


	/**
	 * SMS 보내기
	 * @param array $input
	 *
	 * @access public
	 * @return array
	 */
	public function send($input){
		$today = date('YmdHis');

		$cmid = $this->makeCMID();

		//( SMS 0 / WAP 1 / FAX 2 / PHONE 3 / SMS_INBOUND 4 / MMS 5 )
		switch($input['msg_type']){
			case 'mms':
				$rsMSG_TYPE = 5;
				break;
			default:
				$rsMSG_TYPE = 0;
				break;
		}

		if( empty($cmid)
			|| empty($input['message'])
			|| empty($input['dest_phone'])
			){
			return array('result'=>FALSE, 'msg'=>'필수값 누락');
		}

		$sql = "INSERT INTO ckd_biz_msg
				SET
					CMID = '".$cmid."',
					UMID = '',
					MSG_TYPE = '".$rsMSG_TYPE."',
					STATUS = '0',
					REQUEST_TIME = NOW(),
					SEND_TIME = NOW(),
					DEST_PHONE = '".$input['dest_phone']."',
					DEST_NAME = '".$input['dest_name']."',
					SEND_PHONE = '".$input['send_phone']."',
					SEND_NAME = '".$this->send_name."',
					SUBJECT = '".$input['subject']."',
					MSG_BODY = '".$input['message']."',
					WAP_URL = '".$input['wap_url']."',
					CINFO = '".$input['custom_info']."'
				";
		$result = $this->db->query($sql);
		if($result == TRUE){
			return array('result' => TRUE,
						'msg'=>'발송요청 되었습니다.');
		}else{
			return array('result' => FALSE,
						'msg'=>'DB insert fail');
		}
	}

	/**
	 * 고유값 생성
	 *
	 * @access private
	 * @return string $cmid
	 */
	private function makeCMID(){
		$cmid = date('YmdHis') . '_' . rand(10,99);
		$sql = "SELECT * FROM ckd_biz_msg WHERE CMID = '".$cmid."'";
		$query = $this->db->query($sql);
		if($query->num_rows() > 0){
			$this->makeCMID();
		}else{
			return $cmid;
		}
	}

	private function smsCodeToString($code){
		switch($code){
			case '4100': $message = '전달'; break;
			case '4421': $message = '타임아웃'; break;
			case '4426': $message = '재시도한도초과'; break;
			case '4425': $message = '단말기 호 처리중'; break;
			case '4400': $message = '음영 지역'; break;
			case '4401': $message = '단말기 전원꺼짐'; break;
			case '4402': $message = '단말기 메시지 저장 초과'; break;
			case '4410': $message = '잘못된 번호'; break;
			case '4422': $message = '단말기일시정지'; break;
			case '4427': $message = '기타 단말기 문제'; break;
			case '4405': $message = '단말기busy'; break;
			case '4423': $message = '단말기착신거부'; break;
			case '4412': $message = '착신거절'; break;
			case '4411': $message = 'NPDB에러'; break;
			case '4428': $message = '시스템에러'; break;
			case '4404': $message = '가입자 위치정보 없음'; break;
			case '4413': $message = 'SMSC형식오류'; break;
			case '4414': $message = '비가입자,결번,서비스정지'; break;
			case '4424': $message = 'URL SMS 미지원폰'; break;
			case '4403': $message = '메시지 삭제됨'; break;
			case '4430': $message = '스팸'; break;
			case '4420': $message = '기타 에러'; break;
			case '6600': $message = '전달'; break;
			case '6601': $message = '타임 아웃'; break;
			case '6602': $message = '핸드폰 호 처리 중'; break;
			case '6603': $message = '음영 지역'; break;
			case '6604': $message = '전원이 꺼져 있음'; break;
			case '6605': $message = '메시지 저장개수 초과'; break;
			case '6606': $message = '잘못된 번호'; break;
			case '6607': $message = '서비스 일시 정지';break;
			case '6608': $message = '기타 단말기 문제';break;
			case '6609': $message = '착신 거절';break;
			case '6610': $message = '기타 에러';break;
			case '6611': $message = '통신사의 SMC 형식 오류';break;
			case '6612': $message = '게이트웨이의 형식 오류';break;
			case '6613': $message = '서비스 불가 단말기';break;
			case '6614': $message = '핸드폰 호 불가 상태';break;
			case '6615': $message = 'SMC 운영자에 의해 삭제';break;
			case '6616': $message = '통신사의 메시지 큐 초과';break;
			case '6617': $message = '통신사의 스팸 처리';break;
			case '6618': $message = '공정위의 스팸 처리';break;
			case '6619': $message = '게이트웨이의 스팸 처리';break;
			case '6620': $message = '발송 건수 초과';break;
			case '6621': $message = '메시지의 길이 초과';break;
			case '6622': $message = '잘못된 번호 형식';break;
			case '6623': $message = '잘못된 데이터 형식';break;
			case '6624': $message = 'MMS 정보를 찾을 수 없음';break;
			case '6625': $message = 'NPDB 에러';break;
			case '6626': $message = '080 수신거부(SPAM)';break;
			case '6627': $message = '발송제한 수신거부(SPAM)';break;
			case '6628': $message = '회신번호 차단(개인)';break;
			case '6629': $message = '회신번호 차단(기업)';break;
			case '6630': $message = '서비스 불가 번호';break;
			case '6631': $message = '회신번호 사전 등록제에 의한 미등록 차단';break;
			case '6632': $message = 'KISA 신고 스팸 회신번호 차단';break;
			case '6633': $message = '회신번호 사전 등록제 번호규칙 위반';break;
			case '6634': $message = '첨부파일 사이즈 초과(60K)';break;
			case '9':    $message = '전송 대기';break;
			default:
				$message = '기타(알수없음)';
				break;
		}
		return $message;
	}

}


if(!isset($_sms)) {
	$_sms = new __SMS__;
}
