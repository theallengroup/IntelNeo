<?php
class text_widget extends default_widget {
	function user_interface(){
		#echo(b2());

		/** 
		 * widget test facility
		$w = $this->load_widget('expr');
		$w->set_name($this->get_name());

			$w->set_expr('xor(basic_date,or(basic_date,basic_date))');
		$w->user_interface();
		$w->set_expr('xor(select(basic_date),range(basic_date,basic_date))');

		$w->set_expr('elkin(santiago,sara,victoria,fel(jorge(diana,perro),wilton(mama)),milton(wilfer,alex,bracamonte(hija,hija,hija)))');
		$w->user_interface();
		$w->set_expr('yo(el,a(b(c(d(e),f(g))))),h,i,j,k,l');
		$w->user_interface();
		$w->set_expr('parent(child,child)');
		$w->user_interface();
		$w->set_expr('parent(child,child(kid,kid))');
		$w->user_interface();
		$w->set_expr('parent');
		$w->user_interface();
		$w->set_expr('parent(child,child(kid,kid))');
		$w->user_interface();
		$w->set_expr('1,2,3');
		$w->user_interface();
		$w->set_expr('1,2,3,4,5,123(1,2,3,125(1,2))');
		$w->user_interface();
*/
//		$w->set_expr('xor(select(basic_date),range(basic_date,basic_date))');

		$w = $this->make_replacement('expr');
		$w->set_expr('select(basic_text)');
		#p2($w,'blue');
		
//		return(123);
		return($w->user_interface());
	}
	function text_widget(){

		$this->default_widget();
	}
}
?>
