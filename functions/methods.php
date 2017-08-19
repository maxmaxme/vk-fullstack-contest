<?php

class orders {
	static public function get($act) {
		global $db, $auth;

		$error = '';
		$result = [];

		if (!$auth->user)
			$error = 'Access denied';

		if (!$error) {
			$filters = [
				'new' => ['join' => '', 'cond' => 'o.StatusID=1'],
				'work' => [
					'join' => 'inner join users_by_orders ubo on o.ID=ubo.OrderID and ubo.UserID=' . $auth->user,
					'cond' => 'o.StatusID=2'
				],
				'closed' => [
					'join' => 'inner join users_by_orders ubo on o.ID=ubo.OrderID and ubo.UserID=' . $auth->user,
					'cond' => 'o.StatusID=3'
				]
			];

			$allowed_filters = array_keys($filters);

			$act = in_array($act, $allowed_filters) ?
				$act :
				$allowed_filters[0];


			$result['orders'] = $db->getAll("
			select
				o.ID as OrderID,
				o.Title,
				o.Description,
				o.Reward,
				os.Title as Status,
				o.CreatorID!=?i and StatusID=1 as CanDoBtn,
				o.CreatorID=?i as MyOrder,
				o.StatusID=2 as CanFinishBtn
				
			from orders o
			
			inner join order_status os on o.StatusID=os.ID and os.Hidden=0
			{$filters[$act]['join']}
			
			where 1 and {$filters[$act]['cond']}
			
			order by o.DateTime DESC", $auth->user, $auth->user);


			foreach ($result['orders'] as &$order) {
				// приводим строку к числу, чтобы шаблонизатор нормально воспринимал
				$order['CanDoBtn'] = intval($order['CanDoBtn']);
				$order['MyOrder'] = intval($order['MyOrder']);
				$order['CanFinishBtn'] = intval($order['CanFinishBtn']);
			}

		}



		return [$result, $error];

	}



	static public function doOrder($orderID) {
		global $db, $auth;

		$error = '';
		$result = [];

		if (!$auth->user)
			$error = 'Access denied';
		if (!$orderID)
			$error = 'Заполнены не все поля';
		if (!$db->getOne('select count(*) from orders where ID=?i and StatusID=1', $orderID))
			$error = 'Заказ не найдено';
		if ($db->getOne('select CreatorID=?i from orders where ID=?i', $auth->user, $orderID))
			$error = 'Вы не можете выполнять этот заказ (вы сами его создали)';
		if ($db->getOne('select count(*) from users_by_orders ubo
						inner join orders o on o.ID=ubo.OrderID and o.StatusID=2 
						WHERE ubo.UserID=?i', $auth->user))
			$error = 'Нельзя одновременно выполнять больше одного заказа';

		if (!$error) {
			$db->query('update orders set StatusID=2 where ID=?i', $orderID);
			$db->query('insert into users_by_orders set UserID=?i, OrderID=?i', $auth->user, $orderID);

		}


		return [$result, $error];

	}


	static public function finishOrder($orderID) {
		global $db, $auth;

		$error = '';
		$result = [];

		if (!$auth->user)
			$error = 'Access denied';
		if (!$orderID)
			$error = 'Заполнены не все поля';
		if (!$db->getOne('select count(*) from users_by_orders ubo
						inner join orders o on o.ID=ubo.OrderID and o.StatusID=2 
						WHERE ubo.UserID=?i and ubo.OrderID=?i', $auth->user, $orderID))
			$error = 'Заказ не найден';

		if (!$error) {
			$db->query('update orders set StatusID=3 where ID=?i', $orderID);

		}


		return [$result, $error];

	}

	static public function add($title, $description, $reward) {
		global $db, $auth;

		$error = '';
		$result = [];

		if (!$auth->user)
			$error = 'Access denied';
		if (!$title || !$reward)
			$error = 'Заполнены не все поля';


		if (!$error) {
			$db->query('insert into orders set 
				Title=?s, Description=?s, Reward=?i, CreatorID=?i, StatusID=1, DateTime=NOW()',
				$title, $description, $reward, $auth->user);

		}


		return [$result, $error];

	}
}