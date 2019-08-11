FROM httpd:2.4
WORKDIR /usr/local/apache2/htdocs/
COPY ../dist /usr/local/apache2/htdocs/
# *.deb should only match 1 file
RUN ln -s *.deb latest.deb