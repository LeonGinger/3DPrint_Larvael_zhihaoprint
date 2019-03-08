			<nav class="bar bar-tab">
				<?php 
					$active = 0;
					$url = url()->current();
					if(str_contains($url,'message'))$active = 1;
					if(str_contains($url,'setting'))$active = 2;
					if(str_contains($url,'help'))$active = 3;
				?>
				<a class="tab-item external <?php if($active == 0)echo 'active';?>" href="/customer/orderlist">
				<span class="icon icon-code"></span>
				<span class="tab-label">订单</span>
				</a>
				<a class="tab-item external <?php if($active == 1)echo 'active';?>" href="/customer/message">
				<span class="icon icon-message"></span>
				<span class="tab-label">消息</span>
				</a>
				<a class="tab-item external <?php if($active == 2)echo 'active';?>" href="/customer/setting">
				<span class="icon icon-settings"></span>
				<span class="tab-label">设置</span>
				</a>
				<a class="tab-item external <?php if($active == 3)echo 'active';?>" href="/customer/help">
				<span class="icon icon-browser"></span>
				<span class="tab-label">帮助</span>
				</a>
			</nav>