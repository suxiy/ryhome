<h2>尊敬的管理员:</h2>
<div>算量之家以下项目需要审核,<a href="{{url('admin/app/projects-nochecked')}}">后台地址</a></div>
@foreach($projects as $project)
    <div style="border-top: 1px dotted #999;padding:5px;">
        <div>
            <label>项目编号:</label>
            <span>{{$project->id}}</span>
        </div>
        <div>
            <label>项目类型:</label>
            <span>{{$project->purpose}}</span>
        </div>
        <div>
            <label>联系人:</label>
            <span>{{$project->contactperson}}</span>
        </div>
        <div>
            <label>项目酬劳:</label>
            <span>{{$project->reward}}</span>
        </div>
        <div>
            <label>所在地址:</label>
            <span>{{$project->address}}</span>
        </div>
        <div>
            <label>联系电话:</label>
            <span>{{$project->publishphone}}</span>
        </div>
        <div>
            <label>发布时间:</label>
            <span>{{$project->publishtime}}</span>
        </div>
    </div>
@endforeach
<a href="{{url('')}}">算量之家</a>

