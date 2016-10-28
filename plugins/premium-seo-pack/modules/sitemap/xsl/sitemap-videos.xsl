<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="2.0" 
                xmlns:html="http://www.w3.org/TR/REC-html40"
                xmlns:sitemap="http://www.sitemaps.org/schemas/sitemap/0.9"
                xmlns:video="http://www.google.com/schemas/sitemap-video/1.1"
                xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
	<xsl:output method="html" version="1.0" encoding="UTF-8" indent="yes" />
	<xsl:template match="/">
		<html xmlns="http://www.w3.org/1999/xhtml">
			<head>
				<title>XML Videos Sitemap</title>
				<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
				<meta name="robots" content="noindex,follow" />
				<style type="text/css">
					body {
						font-family: Helvetica, Arial, sans-serif, Verdana;
						font-size: 13px;
					}
					#intro {
						background-color: #CFEBF7;
						border: 1px #2580B2 solid;
						padding: 5px 13px 5px 13px;
						margin: 5px;
					}
					#intro a {
						color: black;
					}
					#intro p {
						line-height: 16.8667px;
					}
					#intro strong {
						font-weight: normal;
					}
					table {
						width: 100%;
					}
					td {
						font-size: 11px;
					}
					td img {
						padding: 0 5px;
					}
					th {
						text-align: left;
						padding-right: 30px;
						font-size: 11px;
						border-bottom: 1px solid #ccc;
						cursor: pointer;
						font-weight: normal;
					}
					tr:hover, tr.high:hover {
						background-color: #ccc;
					}
					tr.high {
						background-color: whitesmoke;
					}
					#footer {
						padding: 2px;
						margin-top: 10px;
						font-size: 8pt;
						color: gray;
					}
					#footer a {
						color: gray;
					}
					#content a {
						color: black;
						text-decoration: none;
					}
					#content a:visited {
						color: #777;
					}
					#content a:hover {
						text-decoration: underline;
					}
					a img {
						border: none;
						width: auto;
						height: 60px;
					}
					#content .images {
						margin-left: 50px;
					}
					#content .images table a {
						display: inline-block;
						margin-bottom: 8px;
					}
				</style>
			</head>
			<body>
				<xsl:apply-templates></xsl:apply-templates>
				<div id="footer">
					Generated with <a rel="external nofollow" target="_blank" href="http://codecanyon.net/item/premium-seo-pack-wordpress-plugin/6109437" title="Premium SEO Pack Wordpress Plugin">Premium SEO Pack Wordpress Plugin</a> by <a rel="external nofollow" target="_blank" href="http://codecanyon.net/user/AA-Team/portfolio" title="AA-Team on CodeCanyon">AA-Team</a>. This XSLT template is released under the GPL and free to use.<br />
					If you have problems with your sitemap please visit the <a rel="external nofollow" target="_blank" href="http://support.aa-team.com/" title="Premium SEO Pack Wordpress Plugin Support Forum">Premium SEO Pack Wordpress Plugin Support Forum</a>.
				</div>
			</body>
		</html>
	</xsl:template>
	
	<xsl:template match="sitemap:urlset">
        <h1>XML Videos Sitemap</h1>
        <div id="intro">
            <p>
                This is a XML Sitemap which is supposed to be processed by search engines which follow the XML Sitemap standard like Google and Bing.<br />
                It was generated using the Blogging-Software <a rel="external nofollow" href="http://wordpress.org/">WordPress</a> and the <strong><a rel="external nofollow" target="_blank" href="http://codecanyon.net/item/premium-seo-pack-wordpress-plugin/6109437" title="Premium SEO Pack Wordpress Plugin">Premium SEO Pack Wordpress Plugin</a></strong>.<br />
                You can find more information about XML sitemaps on <a rel="external nofollow" href="http://sitemaps.org">sitemaps.org</a> and Google's <a rel="external nofollow" href="http://code.google.com/p/sitemap-generators/wiki/SitemapGenerators">list of sitemap programs</a>.<br />
                This sitemap contains <xsl:value-of select="count(sitemap:url)"/> URLs and
                <xsl:value-of select="count(./sitemap:url/video:video)"/> videos.
            </p>
        </div>
		<div id="content">
			<xsl:for-each select="./sitemap:url">
				<div class="post">
					<xsl:variable name="itemURL">
						<xsl:value-of select="sitemap:loc"/>
					</xsl:variable>
					<a href="{$itemURL}" target="_blank">
						<xsl:value-of select="$itemURL"/>
					</a>
				</div>
				<div class="images">
					<table cellpadding="5" id="sitemap">
					<thead>
						<tr style="border-bottom: 1px black solid;">
							<th width="10%">Thumb (<xsl:value-of select="count(video:video)"/>)</th>
							<th width="25%">Title</th>
							<th width="30%">Description</th>
							<th width="20%">Tags</th>
							<th width="15%">Pub Date</th>
						</tr>
					</thead>
					<tbody>
						<xsl:for-each select="./video:video">
							<tr>
								<xsl:if test="position() mod 2 != 1">
									<xsl:attribute name="class">high</xsl:attribute>
								</xsl:if>
									<td>
										<xsl:variable name="thumbURL">
											<xsl:value-of select="./video:thumbnail_loc"/>
										</xsl:variable>
										
										<xsl:variable name="flvURL">
											<xsl:value-of select="./video:player_loc"/>
										</xsl:variable>
										
										<a href="{$flvURL}" target="_blank"><img src="{$thumbURL}" /></a>
									</td>
									
									<td>
										<xsl:variable name="itemURL">
											<xsl:value-of select="sitemap:loc"/>
										</xsl:variable>
										<xsl:variable name="videoTitle">
											<xsl:value-of select="./video:title"/>
										</xsl:variable>
										<a href="{$itemURL}" target="_blank">
											<strong><xsl:value-of select="substring-before(substring-after($videoTitle, 'CDATA['), ']]')"/></strong>
										</a>
									</td>
		
									<td>
										<xsl:variable name="videoDesc">
											<xsl:value-of select="./video:description"/>
										</xsl:variable>
										<xsl:variable name="videoExcerpt">
											<xsl:value-of select="concat(substring($videoDesc,1,500),' ...')"/>
										</xsl:variable>
										<xsl:choose>
											<xsl:when test="string-length($videoDesc) &lt; 200">
												<xsl:value-of select="substring-before(substring-after($videoDesc, 'CDATA['), ']]')"/>
											</xsl:when>
											<xsl:otherwise>
												<xsl:value-of select="substring-before(substring-after($videoExcerpt, 'CDATA['), ']]')"/>
											</xsl:otherwise>
										</xsl:choose>
									</td>
		
									<td>
										<xsl:for-each select="./video:tag">
											<xsl:variable name="videoTag">
												<xsl:value-of select="substring-before(substring-after(., 'CDATA['), ']]')"/>
											</xsl:variable>
											<xsl:value-of select="$videoTag"/>,
										</xsl:for-each>
									</td>
									
									<td>
										<xsl:value-of select="concat(substring(./video:publication_date,0,11),concat(' ', substring(./video:publication_date,12,5)))"/>
									</td>
							</tr>
						</xsl:for-each>
					</tbody>
					</table>
				</div>
			</xsl:for-each>
		</div>
	</xsl:template>
</xsl:stylesheet>