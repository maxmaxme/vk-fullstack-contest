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
				'new' => 'o.StatusID=1',
				'work' => 'o.StatusID=2 and ' . $auth->user . ' IN (ubo.UserID, o.CreatorID)',
				'closed' => 'o.StatusID=3 and ' . $auth->user . ' IN (ubo.UserID, o.CreatorID)'
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
				o.CreatorID!=?i and o.StatusID=1 as CanDoBtn,
				o.CreatorID=?i as MyOrder,
				o.StatusID=2 and o.CreatorID!=?i as CanFinishBtn,
				if(o.StatusID IN (2, 3) and o.CreatorID=?i, u.Name, '') as Executor
				
			from orders o
			
			inner join order_status os on o.StatusID=os.ID and os.Hidden=0
			left join users_by_orders ubo on o.ID=ubo.OrderID
			left join users u on u.ID=ubo.UserID
			
			where 1 and {$filters[$act]}
			
			order by o.DateTime DESC", $auth->user, $auth->user, $auth->user, $auth->user);


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
		elseif (!$orderID)
			$error = 'Заполнены не все поля';
		elseif (!$db->getOne('select count(*) from orders where ID=?i and StatusID=1', $orderID))
			$error = 'Заказ не найдено';
		elseif ($db->getOne('select CreatorID=?i from orders where ID=?i', $auth->user, $orderID))
			$error = 'Вы не можете выполнять этот заказ (вы сами его создали)';
		elseif ($db->getOne('select count(*) from users_by_orders ubo
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
		$result = $orderInfo = [];

		if (!$auth->user)
			$error = 'Access denied';
		elseif (!$orderID)
			$error = 'Заполнены не все поля';
		elseif (!$orderInfo = $db->getRow('select o.ID, o.Reward, o.CreatorID from users_by_orders ubo
						inner join orders o on o.ID=ubo.OrderID and o.StatusID=2 
						WHERE ubo.UserID=?i and ubo.OrderID=?i', $auth->user, $orderID))
			$error = 'Заказ не найден';

		if (!$error) {
			$db->query('update orders set StatusID=3 where ID=?i', $orderID);

			$db->query('update users set Balance=Balance-?i where ID=?i', $orderInfo['Reward'], $orderInfo['CreatorID']);
			$db->query('update users set Balance=Balance+?i where ID=?i', $orderInfo['Reward'], $auth->user);

			$result['balance'] = $db->getOne('select Balance from users where ID=?i', $auth->user);
		}


		return [$result, $error];

	}

	static public function add($title, $description, $reward) {
		global $db, $auth;

		$error = '';
		$result = [];

		$from = ["|[ ]+|is", "|[\n]+|is"];
		$to_description = [" ", "\n"];

		$title = preg_replace($from, ' ', $title);
		$description = preg_replace($from, $to_description, $description);

		if (!$auth->user)
			$error = 'Access denied';
		elseif (!$title || $reward < 1)
			$error = 'Заполнены не все поля';
		elseif ($db->getOne('select 
								sum(o.Reward) # сколько заморожено денег на будущие заказы
							from orders o
							
							where o.StatusID IN (1, 2) and o.CreatorID=?i', $auth->user) + $reward > $auth->balance)
			$error = 'Недостаточно денег на балансе';

		if (!$error) {
			$db->query('insert into orders set 
				Title=?s, Description=?s, Reward=?i, CreatorID=?i, StatusID=1, DateTime=NOW()',
				$title, $description, $reward, $auth->user);

		}


		return [$result, $error];

	}
}