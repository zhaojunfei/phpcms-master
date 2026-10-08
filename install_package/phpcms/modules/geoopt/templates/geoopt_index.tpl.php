<?php defined('IN_ADMIN') or exit('No permission resources.'); include $this->admin_tpl('header', 'admin');?>
<style>
.geo-cards{display:flex;flex-wrap:wrap;gap:14px;margin:14px 0 18px;}
.geo-card{flex:1;min-width:150px;background:#fff;border:1px solid #e5e5e5;border-radius:8px;padding:16px 18px;box-shadow:0 1px 3px rgba(0,0,0,.05);}
.geo-card .num{font-size:28px;font-weight:700;color:#1a7de8;margin:4px 0;}
.geo-card .label{color:#888;font-size:13px;}
.geo-card.green .num{color:#12b76a;}
.geo-card.orange .num{color:#f79009;}
.geo-score{width:130px;height:8px;background:#eef2f6;border-radius:6px;overflow:hidden;display:inline-block;vertical-align:middle;margin-right:6px;}
.geo-score i{display:block;height:100%;background:linear-gradient(90deg,#1a7de8,#37c5a8);border-radius:6px;}
</style>
<div class="pad-lr-10">
<div class="explain-col" style="margin-bottom:6px;">GEO（生成式引擎优化）——让内容更容易被 ChatGPT / 豆包 / 文心一言 / Perplexity 等大模型识别与引用。当前站点 GEO 优化状态概览。</div>

<div class="geo-cards">
  <div class="geo-card"><div class="label">内容总数</div><div class="num"><?php echo $stat['total']; ?></div><div class="label">全部内容模型</div></div>
  <div class="geo-card green"><div class="label">已设摘要 description</div><div class="num"><?php echo $stat['desc']; ?></div><div class="label">大模型可读摘要</div></div>
  <div class="geo-card green"><div class="label">已设关键词 keywords</div><div class="num"><?php echo $stat['keywords']; ?></div><div class="label">主题词覆盖</div></div>
  <div class="geo-card orange"><div class="label">配置了 FAQ 的内容</div><div class="num"><?php echo $stat['faq_contents']; ?></div><div class="label">共 <?php echo $stat['faq_total']; ?> 个问答对</div></div>
  <div class="geo-card <?php echo $stat['schema_enable']?'green':''; ?>"><div class="label">结构化 Schema</div><div class="num"><?php echo $stat['schema_enable']?'已开启':'已关闭'; ?></div><div class="label">JSON-LD 输出</div></div>
  <div class="geo-card"><div class="label">平均完成度</div><div class="num"><?php echo $stat['avg_score']; ?>/100</div><div class="label"><span class="geo-score"><i style="width:<?php echo $stat['avg_score']; ?>%;"></i></span><?php echo $stat['avg_score']; ?>%</div></div>
</div>

<div class="table-list">
<table width="100%" cellspacing="0">
<thead><tr><th align="center">内容模型</th><th align="center">数据表</th><th align="center">内容数</th><th align="center">已设关键词</th><th align="center">已设摘要</th><th align="center">有FAQ内容</th></tr></thead>
<tbody>
<?php if (!empty($stat['models'])) { foreach($stat['models'] as $m) { ?>
<tr>
  <td align="center"><?php echo $m['name']; ?></td>
  <td align="center">v9_<?php echo $m['tablename']; ?></td>
  <td align="center"><?php echo $m['total']; ?></td>
  <td align="center"><?php echo $m['keywords']; ?></td>
  <td align="center"><?php echo $m['desc']; ?></td>
  <td align="center"><?php echo $m['faq_contents']; ?></td>
</tr>
<?php } } else { ?>
<tr><td colspan="6" align="center" style="padding:20px;color:#999">暂无可统计的内容模型</td></tr>
<?php } ?>
</tbody>
</table>
</div>

<div class="explain-col" style="margin-top:14px;color:#888;">
GEO 完成度构成：标题(20) + 关键词(20) + 摘要 description(20) + FAQ 问答对(20) + Schema 结构化(20)，满分 100。
无真实 AI 引用次数统计（需第三方平台 API），本面板为"优化配置健康度"评分。
</div>
</div>
<?php include $this->admin_tpl('footer', 'admin');?>
