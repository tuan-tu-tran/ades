FROM alpine/git as build
RUN mkdir /app
WORKDIR /app
RUN mkdir .git
COPY .git .git
RUN git reset --hard

FROM ades-base as runtime
COPY --from=build /app /ades
RUN chmod a+w /ades/app/cache /ades/app/logs
ENTRYPOINT chmod a+w -R /ades/local && docker-php-entrypoint apache2-foreground