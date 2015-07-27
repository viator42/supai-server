<?php
$dataProvider = $model->search();
$data = $dataProvider->getData();
$page = $dataProvider->getPagination();
?>
<div class="row">
    <div class="col-md-12">
        <div class="row">
            <ol class="breadcrumb">
                <li><a href="<?php echo $this->createUrl("default/index") ?>">Dashboard</a></li>
                <li><a href="<?php echo $this->createUrl("user/admin"); ?>">用户管理</a></li>
                <li class="active">用户列表</li>
                <li class="pull-right">
                    <a href="<?php echo $this->createUrl("create"); ?>" class="btn btn-xs btn-primary">新建</a>
                </li>
            </ol>
        </div>
        <div class="row">
            <?php $form = $this->beginWidget("CActiveForm", array(
                'id' => 'search-form',
                'htmlOptions' => array(
                    'class' => 'form-inline well',
                ),
                'method' => 'GET',
                'action' => $this->createUrl("admin"),
            )); ?>
            <div class="row">
            <div class="col-xs-2">
                <?php echo $form->textField($model, 'id', array('class'=>'form-control', 'placeholder'=>'id')); ?>
            </div>
            <div class="col-xs-2">
                <?php echo $form->textField($model, 'username',array('class'=>'form-control', 'placeholder'=>'用户名')); ?>
            </div>
            <div class="col-xs-2">
                <?php echo $form->textField($model, 'email', array('class'=>'form-control', 'placeholder'=>'邮箱')); ?>
            </div>
            <input type="submit" class="btn btn-primary" value="搜索">
            </div>
            <?php $this->endWidget(); ?>
        </div>
        <div class="row">
            <table class="table table-bordered table-condensed">
            <tr>
                <th style="width: 80px;"><?php echo $model->getAttributeLabel("id"); ?></th>
                <th><?php echo $model->getAttributeLabel("username"); ?></th>
                <th style="width: 300px;"><?php echo $model->getAttributeLabel("email"); ?></th>
                <th style="width: 80px;"><?php echo $model->getAttributeLabel("status"); ?></th>
                <th style="width: 100px;"><?php echo $model->getAttributeLabel("createTime"); ?></th>
                <th style="width: 160px;"><?php echo $model->getAttributeLabel("lastIp"); ?></th>
                <td style="width: 120px;"></td>
            </tr>
            <?php  if ($data): ?>
            <?php  foreach ($data as $v): ?>
            <tr>
                <td><?php echo CHtml::encode($v->id); ?></td>
                <td><?php echo CHtml::encode($v->username); ?></td>
                <td><?php echo CHtml::encode($v->email); ?></td>
                <td><?php echo CHtml::encode($v->displayStatus()); ?></td>
                <td><?php echo CHtml::encode($v->createTime?Util::timeElapsedStr($v->createTime):"-"); ?></td>
                <td><?php echo CHtml::encode($v->lastIp); ?></td>
                <td style="text-align: center;">
                    <div class="btn-group">
                        <a class="btn btn-xs btn-danger" href="<?php echo $this->createUrl("user/view", array("id"=>$v->id)); ?>">查看</a>
                        <a class="btn btn-xs btn-danger" href="<?php echo $this->createUrl("user/delete", array("id"=>$v->id)) ?>">删除</a>
                        <a class="btn btn-xs btn-danger" href="<?php echo $this->createUrl("user/update", array("id"=>$v->id)) ?>">编辑</a>
                    </div>
                </td>
            </tr>
            <?php endforeach; ?>
            <?php else: ?>
            <tr>
                <td colspan="99" style="text-align: center;">暂时没有数据。</td>
            </tr>
            <?php endif; ?>
            </table>
        </div>
    </div>
</div>
