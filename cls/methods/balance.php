<?php
class balance {
	static public function refill($amount) {
		global $db, $auth;

		$result = [];
		$error = '';

		if (!$auth->user)
			$error = 'Access denied';
		elseif ($amount < 1)
			$error = 'Заполнены не все поля';

		if (!$error) {
			$db->query('UPDATE users SET Balance=Balance+?i WHERE ID=?i', $amount, $auth->user);
			$result['balance'] = $db->getOne('select Balance from users where ID=?i', $auth->user);
		}


		return [$result, $error];

	}

	static public function withdraw($amount) {
		global $db, $auth;

		$result = [];
		$error = '';


		if (!$auth->user)
			$error = 'Access denied';
		elseif ($amount < 1)
			$error = 'Заполнены не все поля';
		elseif ($db->getOne('select 
						sum(o.Reward) # сколько заморожено денег на будущие заказы
					from orders o
					
					where o.StatusID IN (1, 2) and o.CreatorID=?i', $auth->user) + $amount > $auth->balance)
			$error = 'Не хватает денег на балансе';

		if (!$error) {
			$db->query('UPDATE users SET Balance=Balance-?i WHERE ID=?i', $amount, $auth->user);
			$result['balance'] = $db->getOne('select Balance from users where ID=?i', $auth->user);
		}



		return [$result, $error];

	}


}