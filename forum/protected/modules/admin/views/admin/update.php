<div class="row">
    <div class="col-md-12">
        <div class="row">
            <ol class="breadcrumb">
                <li><a href="<?php echo $this->createUrl("default/index"); ?>">Dashboard</a></li>
                <li><a href="<?php echo $this->createUrl("admin"); ?>">管理员管理</a></li>
                <li class="active">修改员列表</li>
                <li class="pull-right">
                    <a class="btn btn-xs btn-primary" href="<?php echo $this->createUrl("admin"); ?>">返回列表</a>
                </li>
            </ol>
        </div>
        <div class="row">
        <?php $this->renderPartial('_form', array('model'=>$model)); ?>        </div>
    </div>
</div>

